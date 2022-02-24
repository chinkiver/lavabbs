<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Http\Requests\ReplyRequest;
use Illuminate\Support\Facades\Auth;

class RepliesController extends Controller
{
    public function __construct()
    {
        // 所有操作都需要登录
        $this->middleware('auth');
    }

    /**
     * 保存话题评论
     *
     * @param ReplyRequest $request
     * @param Reply        $reply
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ReplyRequest $request, Reply $reply)
    {
        $reply->content = $request->get('content');
        $reply->user_id = Auth::id();
        $reply->topic_id = $request->topic_id;
        $reply->save();

        return redirect()->to($reply->topic->link())->with('success', '评论创建成功！');
    }

    /**
     * 删除话题评论
     *
     * @param Reply $reply
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('destroy', $reply);
        $reply->delete();

        return redirect()->route('replies.index')->with('success', '评论删除成功！');
    }
}
