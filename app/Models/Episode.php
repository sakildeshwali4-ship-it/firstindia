<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasFactory;

    protected $table = "episodes";

    protected $fillable = [
        'web_series_id',
        'season_id',
        'episode_number',
        'warning_text',
        'video_url',

        'category_id',
        'language_id',
        'cast_id',
        'channel_id',
        'director_id',
        'starring_id',
        'supporting_cast_id',

        'networks',
        'maturity_rating',
        'name',

        'thumbnail',
        'landscape',
        'trailer_url',

        'release_year',
        'age_restriction',
        'max_video_quality',
        'release_tag',

        'type_id',
        'video_type',
        'video_upload_type',
        'video_extension',

        'is_like',
        'is_dislike',
        'is_superlike',
        'is_premium',

        'description',
        'video_duration',
        'video_size',
        'view',
        'imdb_rating',
        'download',

        'status',
        'is_title',

        'video_320',
        'video_480',
        'video_720',
        'video_1080',

        'subtitle_type',
        'subtitle_lang_1',
        'subtitle_lang_2',
        'subtitle_lang_3',

        'subtitle_1',
        'subtitle_2',
        'subtitle_3',
    ];

    protected $casts = [
        'imdb_rating' => 'float',
        'video_duration' => 'integer',
        'video_size' => 'integer',
        'view' => 'integer',
        'download' => 'integer',

        'is_like' => 'string',
        'is_dislike' => 'string',
        'is_superlike' => 'string',
        'is_premium' => 'string',
        'status' => 'string',
    ];

    public function webSeries()
    {
        return $this->belongsTo(WebSeries::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', '1');
    }
    
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }
}
