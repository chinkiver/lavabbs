<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SyncUserActivatedAt extends Command
{
    protected $signature = 'larabbs:sync-user-activated-at';
    protected $description = '将用户最后登录时间从 Redis 同步到数据库中';

    public function handle(User $user)
    {
        $user->syncUserActivatedAt();
        $this->info("同步成功！");
    }
}
