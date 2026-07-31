<?php

namespace App\Models\Reels;

use Illuminate\Database\Eloquent\Model;

class WatchProgress extends Model
{
    protected $fillable = [
        'user_id',
        'guest_id',
        'episode_id',
        'progress_seconds',
        'completed',
    ];

    protected function casts(): array
    {
        return [
            'progress_seconds' => 'integer',
            'completed' => 'boolean',
        ];
    }
}
