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
        1 => 'apple.png',
        2 => 'grapes.png',
        3 => 'lemon.png',
        4 => 'mango.png',
        5 => 'orange.png',
    ];

    return $onlyAvatarArray ? $avatar : $avatar[random_int(1, 5)];
}

/**
 * 根据路由信息，获取当前反问话题种类
 *
 * @param $category_id
 *
 * @return string
 */
function category_nav_active($category_id)
{
    return active_class((if_route('categories.show') && if_route_param('category', $category_id)));
}

/**
 * 摘录
 *
 * @param     $value
 * @param int $length
 *
 * @return mixed
 */
function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));

    return \Str::limit($excerpt, $length);
}

function model_admin_link($title, $model)
{
    return model_link($title, $model, 'admin');
}

function model_link($title, $model, $prefix = '')
{
    // 获取数据模型的复数蛇形命名
    $model_name = model_plural_name($model);

    // 初始化前缀
    $prefix = $prefix ? "/$prefix/" : '/';

    // 使用站点 URL 拼接全量 URL
    $url = config('app.url') . $prefix . $model_name . '/' . $model->id;

    // 拼接 HTML A 标签，并返回
    return '<a href="' . $url . '" target="_blank">' . $title . '</a>';
}

function model_plural_name($model)
{
    // 从实体中获取完整类名，例如：App\Models\User
    $full_class_name = get_class($model);

    // 获取基础类名，例如：传参 `App\Models\User` 会得到 `User`
    $class_name = class_basename($full_class_name);

    // 蛇形命名，例如：传参 `User`  会得到 `user`, `FooBar` 会得到 `foo_bar`
    $snake_case_name = Str::snake($class_name);

    // 获取子串的复数形式，例如：传参 `user` 会得到 `users`
    return Str::plural($snake_case_name);
}
