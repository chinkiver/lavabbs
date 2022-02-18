<?php

use Illuminate\Support\Facades\Route;

/**
 * 通过获取当前路由名称，转换为样式的 class 名
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}
