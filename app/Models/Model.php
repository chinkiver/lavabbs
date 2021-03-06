<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    use HasFactory;

    /**
     * ζ ID εεΊ
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('id', 'desc');
    }
}
