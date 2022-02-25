<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CalculateActiveUser extends Command
{
    protected $signature = 'larabbs:calculate-active-user';
    protected $description = '生成活跃用户';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(User $user)
    {
        // 在命令行打印一行信息
        $this->info("开始计算...");

        $user->calculateAndCacheActiveUsers();

        $this->info("成功生成！");
    }
}
