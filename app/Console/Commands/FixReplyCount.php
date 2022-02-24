<?php

namespace App\Console\Commands;

use App\Models\Topic;
use Illuminate\Console\Command;

class FixReplyCount extends Command
{
    protected $signature = 'larabbs:fix:reply_count';
    protected $description = '修复话题评论数';

    public function handle()
    {
        $topics = Topic::all();

        foreach ($topics as $topic) {
            $topic->reply_count = $topic->replies->count();
            $topic->save();
        }
    }
}
