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
}
