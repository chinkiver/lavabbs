<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, MustVerifyEmailTrait;

    use Notifiable {
        // 将 Notifiable 中的 notify 方法重新命名为 laravelNotify
        notify as protected laravelNotify;
    }

    // laravel-permission 提供的 Trait
    use HasRoles;

    // 统计活跃用户数
    use Traits\ActiveUserHelper;

    /**
     * 自定义 notify
     *
     * @param $instance
     */
    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id === Auth::id()) {
            return;
        }

        // 只有数据库类型通知才需提醒，直接发送 Email 或者其他的都 Pass
        if (method_exists($instance, 'toDatabase')) {
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'introduction',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 用户所发布的多个话题
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topics()
    {
        return $this->hasMany(Topic::class, 'user_id', 'id');
    }

    /**
     * 判断是否为当前记录的拥有者
     *
     * @param $model
     *
     * @return bool
     */
    public function isAuthorOf($model)
    {
        return $this->id === $model->user_id;
    }

    /**
     * 该用户所回复的内容
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    /**
     * 修改字段 password 写入数据库前的操作
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        if (strlen($value) != 60) {
            // 不等于 60，做密码加密处理
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    /**
     * 修改 avatar 保存规则
     *
     * @param $path
     */
    public function setAvatarAttribute($path)
    {
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if (! \Str::startsWith($path, 'http')) {
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }

        $this->attributes['avatar'] = $path;
    }
}
