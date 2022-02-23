<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Topic;

class CategoriesController extends Controller
{
    /**
     * 根据分类显示话题列表
     *
     * @param Category $category
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Category $category)
    {
        $topics = Topic::where('category_id', $category->id)->paginate(10);

        return view('topics.index', compact('topics', 'category'));
    }
}
