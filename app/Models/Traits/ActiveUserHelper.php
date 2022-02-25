<?php

namespace App\Models\Traits;

use App\Models\Reply;
use App\Models\Topic;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 活跃用户
 * 系统 每一个小时 计算一次，统计 最近 7 天 所有用户发的 帖子数 和 评论数，
 * 用户每发一个帖子则得 4 分，每发一个回复得 1 分，计算出所有人的『得分』后再倒序，排名前八的用户将会显示在「活跃用户」列表里。
 * Trait ActiveUserHelper
 *
 * @package App\Models\Traits
 */
trait ActiveUserHelper
{
    // 用于存放临时用户数据
    protected $users = [];

    // 配置信息
    protected $topicWeight = 4; // 话题权重
    protected $replyWeight = 1; // 回复权重
    protected $passDays = 7; // 多少天内发表过内容
    protected $userNumber = 6; // 取出来多少用户

    // 缓存相关配置
    protected $cacheKey = 'larabbs_active_users';
    protected $cacheExpireInSeconds = 65 * 60;

    public function getActiveUsers()
    {
        // 尝试从缓存中取出 cache_key 对应的数据。如果能取到，便直接返回数据。
        // 否则运行匿名函数中的代码来取出活跃用户数据，返回的同时做了缓存。
        return Cache::remember($this->cacheKey, $this->cacheExpireInSeconds, function() {
            return $this->calculateActiveUsers();
        });
    }

    public function calculateAndCacheActiveUsers()
    {
        // 取得活跃用户列表
        $active_users = $this->calculateActiveUsers();

        // 并加以缓存
        $this->cacheActiveUsers($active_users);
    }

    /**
     * 计算并获取活跃用户
     *
     * @return \Illuminate\Support\Collection
     */
    private function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        // 数组按照得分排序
        $users = Arr::sort($this->users, function($user) {
            return $user['score'];
        });

        // 我们需要的是倒序，高分靠前，第二个参数为保持数组的 KEY 不变
        $users = array_reverse($users, true);

        // 只获取我们想要的数量
        $users = array_slice($users, 0, $this->userNumber, true);

        // 新建一个空集合
        $active_users = collect();

        foreach ($users as $user_id => $user) {
            // 找寻下是否可以找到用户
            $user = $this->find($user_id);

            // 如果数据库里有该用户的话
            if ($user) {

                // 将此用户实体放入集合的末尾
                $active_users->push($user);
            }
        }

        // 返回数据
        return $active_users;
    }

    private function calculateTopicScore()
    {
        // 从话题数据表里取出限定时间范围（$passDays）内，有发表过话题的用户
        // 并且同时取出用户此段时间内发布话题的数量
        $topic_users = Topic::query()->select(DB::raw('user_id, count(*) as topic_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->passDays))
            ->groupBy('user_id')
            ->get();

        // 根据话题数量计算得分
        foreach ($topic_users as $value) {
            $this->users[$value->user_id]['score'] = $value->topic_count * $this->topicWeight;
        }
    }

    private function calculateReplyScore()
    {
        // 从回复数据表里取出限定时间范围（$pass_days）内，有发表过回复的用户
        // 并且同时取出用户此段时间内发布回复的数量
        $reply_users = Reply::query()->select(DB::raw('user_id, count(*) as reply_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->passDays))
            ->groupBy('user_id')
            ->get();

        // 根据回复数量计算得分
        foreach ($reply_users as $value) {
            $reply_score = $value->reply_count * $this->replyWeight;

            if (isset($this->users[$value->user_id])) {
                $this->users[$value->user_id]['score'] += $reply_score;
            } else {
                $this->users[$value->user_id]['score'] = $reply_score;
            }
        }
    }

    private function cacheActiveUsers($active_users)
    {
        // 将数据放入缓存中
        Cache::put($this->cacheKey, $active_users, $this->cacheExpireInSeconds);
    }
}
