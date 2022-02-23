<?php

namespace App\Models;

class Category extends Model
{
    // 禁用时间戳
    public $timestamps = false;

    // 可被批量编辑的字段
    protected $fillable = [
        'name', 'description',
    ];
}
