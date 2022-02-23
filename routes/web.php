<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 首页
Route::get('/', 'PagesController@root')->name('root');

// 认证脚手架所生成的路由
Auth::routes(['verify' => true]);

// 用户管理
Route::resource('/users', 'UsersController', [
    'only' => ['show', 'update', 'edit'],
]);

// 话题
Route::resource('topics', 'TopicsController', [
    'only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy'],
]);

// 分类
Route::resource('categories', 'CategoriesController', [
    'only' => ['show']
]);

// 上传图片
Route::post('upload_image', 'TopicsController@uploadImage')->name('topics.upload_image');
