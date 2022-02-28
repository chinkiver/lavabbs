<?php

namespace App\Http\Requests\Api\V1;

class UserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|between:3,50|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name',
            'username' => 'required|between:2,50',
            'password' => 'required|alpha_dash|min:6',
            'verification_key' => 'required|string',
            'verification_code' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码',
        ];
    }
}
