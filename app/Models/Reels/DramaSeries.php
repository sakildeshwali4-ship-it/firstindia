<?php

namespace App\Models\Reels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DramaSeries extends Model
{
    protected $table = 'drama_series';

    protected $hidden = [
        'number_of_free_episodes',
    ];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'poster_url',
        'cover_url',
        'genre',
        'language',
        'rating',
        'total_episodes',
        'is_premium',
        'coin_price',
        'number_of_free_episodes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'total_episodes' => 'integer',
            'is_premium' => 'boolean',
            'coin_price' => 'integer',
            'number_of_free_episodes' => 'integer',
        ];
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class, 'series_id')->orderBy('number');
    }
}
