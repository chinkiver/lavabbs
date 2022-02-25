<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PagesController extends Controller
{
    /**
     * 首页
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function root()
    {
        // 判断用户是否验证了邮箱
//        dd(\Auth::user()->hasVerifiedEmail());

        $title = 'LavaBBS';
        return view('pages.root', compact('title'));
    }

    public function permissionDenied()
    {
        // 如果当前用户有权限访问后台，直接跳转访问
        if (config('administrator.permission')()) {
            return redirect(url(config('administrator.uri')), 302);
        }

        // 否则使用视图
        return view('pages.permission_denied');
    }
}
