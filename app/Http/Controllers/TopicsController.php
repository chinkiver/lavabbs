<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\Topic;
use App\Http\Requests\TopicRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * 显示话题列表页
     *
     * @param Request $request
     * @param Topic   $topic
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request, Topic $topic, User $user)
    {
        // 这里的 User $user 只是一个便捷的 $user = new User 写法而已

        $topics = $topic->withOrder($request->order)
            ->with('user', 'category')
            ->paginate(10);

        $activeUsers = $user->getActiveUsers();

        return view('topics.index', compact('topics', 'activeUsers'));
    }

    /**
     * 显示话题详细
     *
     * @param Request $request
     * @param Topic   $topic
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function show(Request $request, Topic $topic)
    {
        // URL 矫正
        if (! empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic'));
    }

    /**
     * 显示创建话题页面
     *
     * @param Topic $topic
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Topic $topic)
    {
        $categories = Category::all();

        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    /**
     * 保存话题
     *
     * @param TopicRequest $request
     * @param Topic        $topic
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TopicRequest $request, Topic $topic)
    {
        // 将传参的键值数组填充到模型的属性中
        $topic->fill($request->all());

        $topic->user_id = Auth::id();

        $topic->save();

        return redirect()->to($topic->link())->with('success', '帖子创建成功！');
    }

    /**
     * 显示编辑话题页面
     *
     * @param Topic $topic
     *
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Topic $topic)
    {
        // Police
        $this->authorize('update', $topic);

        $categories = Category::all();

        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    /**
     * 保存编辑后的话题
     *
     * @param TopicRequest $request
     * @param Topic        $topic
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());

        return redirect()->to($topic->link())->with('success', '编辑成功！');
    }

    /**
     * 删除话题
     *
     * @param Topic $topic
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();

        return redirect()->route('topics.index')->with('success', '成功删除！');
    }

    /**
     * 上传图片
     *
     * @param Request            $request
     * @param ImageUploadHandler $uploadHandler
     *
     * @return array
     */
    public function uploadImage(Request $request, ImageUploadHandler $uploadHandler)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success' => false,
            'msg' => '上传失败!',
            'file_path' => '',
        ];

        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_image) {
            // 保存图片到本地
            $result = $uploadHandler->store($file, 'topics', \Auth::id(), 1024);

            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg'] = "上传成功!";
                $data['success'] = true;
            }
        }

        return $data;
    }
}
