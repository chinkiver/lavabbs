<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;

class EmailVerified
{
    public function __construct()
    {
        //
    }

    public function handle(Verified $event)
    {
        session()->flash('success', '邮箱认证成功！');
    }
}
