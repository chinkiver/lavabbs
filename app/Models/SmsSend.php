<?php

namespace App\Models;

class SmsSend extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'phone', 'code', 'key',
    ];
}
