<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Link;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * 根据分类显示话题列表
     *
     * @param Category $category
     * @param Request  $request
     * @param Topic    $topic
     * @param User     $user
     * @param Link     $link
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Category $category, Request $request, Topic $topic, User $user, Link $link)
    {
        $topics = $topic->withOrder($request->order)
            ->with('user', 'category')
            ->where('category_id', $category->id)
            ->paginate(10);

        // 活跃用户列表
        $activeUsers = $user->getActiveUsers();

        // 推荐内容
        $links = $link->getAllCached();

        return view('topics.index', compact('topics', 'category', 'activeUsers', 'links'));
    }
}
