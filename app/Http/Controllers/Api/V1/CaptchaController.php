<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\CaptchaRequest;
use App\Models\Captcha;
use App\Models\Enums\CaptchaType;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CaptchaController extends Controller
{
    /**
     * 获取图形验证码
     *
     * @param CaptchaRequest $request
     * @param CaptchaBuilder $builder
     * @param Captcha        $captchaModel
     *
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function store(CaptchaRequest $request, CaptchaBuilder $builder, Captcha $captchaModel)
    {
        // Key
        $key = 'captcha_' . Str::random(15);

        // 电话
        $phone = $request->phone;

        // 图形验证码的值
        $captcha = $builder->build();

        // 设定过期时间，2 分钟后
        $expiredAt = now()->addMinutes(2);

        // 缓存
        Cache::put($key, [
            'phone' => $phone,
            'code' => $captcha->getPhrase(),
        ], $expiredAt);

        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(), // 过期时间
            'captcha_image_content' => $captcha->inline(), // base64 图片验证码
        ];

        $captchaModel->type = CaptchaType::GRAPH;
        $captchaModel->phone = $phone;
        $captchaModel->code = $captcha->getPhrase();
        $captchaModel->key = $key;
        $captchaModel->send_time = Carbon::now();
        $captchaModel->expired_at = $expiredAt;
        $captchaModel->save();

        return response()->json($result)->setStatusCode(201);
    }
}
