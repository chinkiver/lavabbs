<?php

namespace App\Observers;

use App\Models\Reply;

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        // XSS 安全问题
        $reply->content = clean($reply->content, 'user_topic_body');
    }

    public function created(Reply $reply)
    {
        $reply->topic->reply_count = $reply->topic->replies->count();
        $reply->topic->save();
    }
}
