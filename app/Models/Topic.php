<?php

namespace App\Models;

class Topic extends Model
{
    // 可被批量编辑的字段
    protected $fillable = [
        'title', 'body', 'category_id', 'excerpt', 'slug',
    ];

    /**
     * 所属话题种类
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * 发布的作者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 根据 order 排序
     *
     * @param $query
     * @param $order
     */
    public function scopeWithOrder($query, $order)
    {
        switch ($order) {
            case 'recent':
                $query->recent();
                break;

            default:
                $query->recentReplied();
                break;
        }
    }

    /**
     * 按最后修改时间排序
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeRecentReplied($query)
    {
        // 当话题有新回复时，我们将编写逻辑来更新话题模型的 reply_count 属性，
        // 此时会自动触发框架对数据模型 updated_at 时间戳的更新
        return $query->orderBy('updated_at', 'desc');
    }

    /**
     * 按创建时间排序
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeRecent($query)
    {
        // 按照创建时间排序
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * 将路由 topics.show 进行转换
     *
     * @param array $params
     *
     * @return string
     */
    public function link($params = [])
    {
        // array_merge 把多个数组合成一个数组
        return route('topics.show', array_merge([$this->id, $this->slug], $params));
    }

    /**
     * 一个话题拥有的回复
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * 更新回复数量
     */
    public function updateReplyCount()
    {
        $this->reply_count = $this->replies->count();
        $this->save();
    }
}
