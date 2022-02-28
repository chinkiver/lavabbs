<?php

namespace App\Models\Enums;

class CaptchaType
{
    const SMS = '短信验证码';
    const GRAPH = '图形验证码';

    const TYPES = [
        'SMS' => self::SMS,
        'GRAPH' => self::GRAPH,
    ];
}
