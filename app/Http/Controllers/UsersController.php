<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    public function __construct()
    {
        // 登录中间件
        $this->middleware('auth', [
            'except' => ['show'], // 除了显示用户详细，其他都需要登录
        ]);
    }

    /**
     * 显示用户详细信息
     *
     * @param User $user
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * 显示编辑用户信息窗口
     *
     * @param User $user
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(User $user)
    {
        // 判断是否为其他用户
        $this->authorize('canEdit', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * 更新用户信息
     *
     * @param UserRequest        $request
     * @param ImageUploadHandler $uploadHandler
     * @param User               $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, ImageUploadHandler $uploadHandler, User $user)
    {
        // 判断是否为其他用户
        $this->authorize('canEdit', $user);

        // 获取全部表单信息
        $data = $request->all();

        // 如果存在文件信息
        if ($request->avatar) {
            $uploadResult = $uploadHandler->store($request->avatar, 'avatars', $user->id, 416);

            if ($uploadResult) {
                $data['avatar'] = $uploadResult['path'];
            }
        }

        // 更新用户信息
        $user->update($data);

        // 返回
        return redirect()->route('users.show', $user)->with('success', '用户信息修改成功！');
    }
}
