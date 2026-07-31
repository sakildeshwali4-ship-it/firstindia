<?php

namespace App\Models\Reels;

use App\Models\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEpisodeLike extends Model
{
    protected $table = 'user_episode_likes';

    protected $fillable = [
        'user_id',
        'episode_id',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'episode_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class, 'episode_id');
    }
}
