<?php

namespace App\Http;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,

        // 修正代理服务器后的服务器参数
        \App\Http\Middleware\TrustProxies::class,

        // 解决 cors 跨域问题
        \Fruitcake\Cors\HandleCors::class,

        // 检测应用是否进入『维护模式』
        // 见：https://learnku.com/docs/laravel/8.x/configuration#maintenance-mode
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // 检测表单请求的数据是否过大
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // 对所有提交的请求数据进行 PHP 函数 `trim()` 处理
        \App\Http\Middleware\TrimStrings::class,

        // 将提交请求参数中空子串转换为 null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // Cookie 加密解密
            \App\Http\Middleware\EncryptCookies::class,

            // 将 Cookie 添加到响应中
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,

            // 开启会话
            \Illuminate\Session\Middleware\StartSession::class,

            // \Illuminate\Session\Middleware\AuthenticateSession::class,

            // 将系统的错误数据注入到视图变量 $errors 中
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // 检验 CSRF ，防止跨站请求伪造的安全威胁
            // 见：https://learnku.com/docs/laravel/8.x/csrf
            \App\Http\Middleware\VerifyCsrfToken::class,

            // 处理路由绑定
            // 见：https://learnku.com/docs/laravel/8.x/routing#route-model-binding
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // 强制用户邮箱认证
            \App\Http\Middleware\EnsureEmailIsVerified::class, // 增加验证邮箱认证中间件

            // 记录用户最后活跃时间
            \App\Http\Middleware\RecordLastActivedTime::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\AcceptHeader::class, // 给 api 路由添加 Accept = application/json
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

    /**
     * 定时任务
     *
     * @param Schedule $schedule
     */
    public function schedule(Schedule $schedule)
    {
        // 每隔一个小时执行一遍
        $schedule->command('larabbs:calculate-active-user')->hourly();

        // 每日零时执行一次
        $schedule->command('larabbs:sync-user-activated-at')->dailyAt('00:00');
    }
}
