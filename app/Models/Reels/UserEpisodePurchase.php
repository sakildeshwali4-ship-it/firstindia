<?php

namespace App\Models\Reels;

use App\Models\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEpisodePurchase extends Model
{
    protected $fillable = [
        'user_id',
        'episode_id',
        'coins_spent',
        'purchased_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'episode_id' => 'integer',
        'coins_spent' => 'integer',
        'purchased_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class, 'episode_id');
    }
}
