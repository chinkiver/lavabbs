<?php

use Illuminate\Support\Facades\Route;

/**
 * 通过获取当前路由名称，转换为样式的 class 名
 *
 * @return string
 */
function route_class():string
{
    return str_replace('.', '-', Route::currentRouteName());
}

/**
 * 随机头像图片
 *
 * @param bool $onlyAvatarArray 是否仅返回头像数组
 *
 * @return array|string
 * @throws Exception
 */
function random_avatar(bool $onlyAvatarArray = false):array|string
{
    static $avatar = [
        1 => 'images/avatar/apple.png',
        2 => 'images/avatar/grapes.png',
        3 => 'images/avatar/lemon.png',
        4 => 'images/avatar/mango.png',
        5 => 'images/avatar/orange.png',
    ];

    return $onlyAvatarArray ? $avatar : $avatar[random_int(1, 5)];
}
