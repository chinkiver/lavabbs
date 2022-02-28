<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->namespace('Api\\V1')->name('api.v1.')->group(function() {

    // 显示 Version
    Route::get('version', function() {
        return '当前请求的接口版本为 V1.0';
    })->name('version');

    // 发送短信验证码
    Route::post('verificationCodes', 'VerificationCodesController@store')
        ->name('verificationCodes.store');
});

Route::prefix('v2')->name('api.v2.')->group(function() {
    Route::get('version', function() {
        return '当前请求的接口版本为 V2.0';
    })->name('version');
});
