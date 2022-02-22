<?php

use Illuminate\Support\Facades\Route;

/**
 * 通过获取当前路由名称，转换为样式的 class 名
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

/**
 * 随机头像图片
 *
 * @return int
 * @throws Exception
 */
function random_avatar()
{
    static $avatar = [
        1 => 'images/avatar/apple.png',
        2 => 'images/avatar/grapes.png',
        3 => 'images/avatar/lemon.png',
        4 => 'images/avatar/mango.png',
        5 => 'images/avatar/orange.png',
    ];

    return $avatar[random_int(1, 5)];
}
