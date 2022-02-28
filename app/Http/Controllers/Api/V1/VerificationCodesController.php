<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\VerificationCodeRequest;
use App\Models\Captcha;
use App\Models\Enums\CaptchaType;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{

    /**
     * 用户注册，发送短信验证码
     *
     * @param VerificationCodeRequest $request
     * @param EasySms                 $easySms
     * @param Captcha                 $captchaModel
     *
     * @return \Illuminate\Http\JsonResponse|object
     * @throws \Overtrue\EasySms\Exceptions\InvalidArgumentException
     */
    public function store(VerificationCodeRequest $request, EasySms $easySms, Captcha $captchaModel)
    {
        // 从缓存中获取图形验证码
        $captchaData = Cache::get($request->captcha_key);

        if (! $captchaData) {
            abort(403, '图片验证码已失效');
        }

        if (! hash_equals(strtolower($captchaData['code']), strtolower($request->captcha_code))) {
            // 验证错误就清除缓存
            Cache::forget($request->captcha_key);
            throw new AuthenticationException('验证码错误');
        }

        // 获得手机号码
        $phone = $captchaData['phone'];

        // 生成随机 4 位数，左补 0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        if (config('easysms.enable')) {

            // 发送短信
            try {
                $result = $easySms->send($phone, [
                    'template' => config('easysms.gateways.aliyun.templates.register'),
                    'data' => [
                        'code' => $code,
                    ],
                ]);
            } catch (NoGatewayAvailableException $exception) {
                $message = $exception->getException('aliyun')->getMessage();
                abort(500, $message ? : '短信发送异常');
            }

        }

        // Key
        $key = 'verificationCode_' . Str::random(15);

        // 设定缓存时间
        $expiredAt = now()->addMinutes(5);

        // 缓存验证码 5 分钟过期。
        Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        // 数据库记录
        $captchaModel->type = CaptchaType::SMS;
        $captchaModel->phone = $phone;
        $captchaModel->code = $code;
        $captchaModel->key = $key;
        $captchaModel->send_time = Carbon::now();
        $captchaModel->expired_at = $expiredAt;
        $captchaModel->save();

        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
