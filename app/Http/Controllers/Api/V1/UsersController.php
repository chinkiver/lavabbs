<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{
    public function store(UserRequest $request, User $user)
    {
        // 获取缓存中的验证码
        $verifyData = Cache::get($request->verification_key);

        // 验证码是否为空
        if (! $verifyData) {
            abort(403, '验证码已失效');
        }

        // 验证码是否正确
        // hash_equals 可防止时序攻击的字符串比较
        // 两个字符串是从第一位开始逐一进行比较的，发现不同就立即返回 false，那么通过计算返回的速度就知道了大概是哪一位开始不同的
        // 而使用 hash_equals 比较两个字符串，无论字符串是否相等，函数的时间消耗是恒定的，这样可以有效的防止时序攻击
        if (! hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            throw new AuthenticationException('验证码错误');
        }

        // 用户注册
        $user->name = $request->name;
        $user->username = $request->username;
        $user->phone = $verifyData['phone'];
        $user->password = $request->password; // 密码会自动加密，App\Models\User
        $user->save();

        // 清除缓存
        Cache::forget($request->verification_key);

        return new UserResource($user);
    }
}
