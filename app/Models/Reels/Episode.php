<?php

namespace App\Models\Reels;

use App\Models\Concerns\ResolvesMediaUrls;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Episode extends Model
{
    use ResolvesMediaUrls;

    protected $table = 'reel_episodes';

    protected $fillable = [
        'series_id',
        'number',
        'title',
        'synopsis',
        'thumbnail_url',
        'video_url',
        'duration_seconds',
        'is_locked',
        'coin_price',
        'is_premium',
        'show_like_button',
        'show_watchlist_button',
        'show_share_button',
        'show_episodes_button',
        'likes',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'duration_seconds' => 'integer',
            'is_locked' => 'boolean',
            'coin_price' => 'integer',
            'is_premium' => 'boolean',
            'show_like_button' => 'boolean',
            'show_watchlist_button' => 'boolean',
            'show_share_button' => 'boolean',
            'show_episodes_button' => 'boolean',
            'likes' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(DramaSeries::class, 'series_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(UserEpisodePurchase::class, 'episode_id');
    }

    public function userLikes(): HasMany
    {
        return $this->hasMany(UserEpisodeLike::class, 'episode_id');
    }

    public function getThumbnailUrlAttribute(?string $value): ?string
    {
        return $this->resolveMediaUrl($value);
    }

    public function getVideoUrlAttribute(?string $value): ?string
    {
        return $this->resolveMediaUrl($value);
    }
}
