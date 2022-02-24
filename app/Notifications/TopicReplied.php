<?php

namespace App\Notifications;

use App\Models\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TopicReplied extends Notification implements ShouldQueue
{
    use Queueable;

    public $reply;

    public function __construct(Reply $reply)
    {
        $this->reply = $reply;
    }

    /**
     * 通知渠道
     *
     * @param $notifiable
     *
     * @return string[]
     */
    public function via($notifiable)
    {
        // 决定了通知在哪个频道上发送
        return ['database', 'mail'];
    }

    /**
     * 保存至数据库
     *
     * @param $notifiable
     *
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $topic = $this->reply->topic;
        $link = $topic->link(['#reply' . $this->reply->id]);

        // 存入数据库里的数据
        // 返回的数组将被转成 JSON 格式并存储到通知数据表的 data 字段中
        return [
            'reply_id' => $this->reply->id,
            'reply_content' => $this->reply->content,
            'user_id' => $this->reply->user->id,
            'user_name' => $this->reply->user->name,
            'user_avatar' => $this->reply->user->avatar,
            'topic_link' => $link,
            'topic_id' => $topic->id,
            'topic_title' => $topic->title,
        ];
    }

    /**
     * 发送邮件
     *
     * @param $notifiable
     *
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $url = $this->reply->topic->link(['#reply' . $this->reply->id]);

        return (new MailMessage)
            ->line('你的话题有新回复！')
            ->action('查看回复', $url);
    }
}
