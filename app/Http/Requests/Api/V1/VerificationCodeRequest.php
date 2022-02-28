<?php

namespace App\Http\Requests\Api\V1;

class VerificationCodeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'phone' => [
                'required',
//                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199)\d{8}$/',
                'phone:CN,mobile', // 使用 propaganistas/laravel-phone 扩展包
                'unique:users',
            ],
        ];
    }
}
