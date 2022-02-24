<?php

namespace App\Models;

/**
 * 话题回复
 * Class Reply
 *
 * @package App\Models
 */
class Reply extends Model
{
    protected $fillable = ['content'];

    /**
     * 该回复对应的话题
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * 谁回复的话题
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
