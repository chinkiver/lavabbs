<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * 首页
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function root()
    {
        $title = 'LavaBBS';
        return view('pages.root', compact('title'));
    }
}
