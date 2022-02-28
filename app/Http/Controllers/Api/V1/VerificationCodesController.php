<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\VerificationCodeRequest;
use App\Models\SmsSend;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{

    /**
     * 发送短信验证码
     *
     * @param VerificationCodeRequest $request
     * @param EasySms                 $easySms
     * @param SmsSend                 $smsSend
     *
     * @return \Illuminate\Http\JsonResponse|object
     * @throws \Overtrue\EasySms\Exceptions\InvalidArgumentException
     */
    public function store(VerificationCodeRequest $request, EasySms $easySms, SmsSend $smsSend)
    {
        // Key
        $key = 'verificationCode_' . Str::random(15);

        // 获得手机号码
        $phone = $request->phone;

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

        } else {

            // 不发送短信，数据库记录
            $smsSend->phone = $phone;
            $smsSend->code = $code;
            $smsSend->key = $key;
            $smsSend->send_time = Carbon::now();
            $smsSend->save();
        }

        // 设定缓存时间
        $expiredAt = now()->addMinutes(5);

        // 缓存验证码 5 分钟过期。
        Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
