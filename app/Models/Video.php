<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'video';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'channel_id' => 'integer',
        'category_id' => 'string',
        'language_id' => 'string',
        'cast_id' => 'string',
        'type_id' => 'integer',
        'video_type' => 'integer',
        'name' => 'string',
        'thumbnail' => 'string',
        'landscape' => 'string',
        'description' => 'string',
        'is_premium' => 'integer',
        'is_title' => 'string',
        'download' => 'integer',
        'video_upload_type' => 'string',
        'video_320' => 'string',
        'video_480' => 'string',
        'video_720' => 'string',
        'video_1080' => 'string',
        'video_extension' => 'string',
        'video_duration' => 'integer',
        'trailer_type' => 'string',
        'trailer_url' => 'string',
        'subtitle_type' => 'string',
        'subtitle_lang_1' => 'string',
        'subtitle_1' => 'string',
        'subtitle_lang_2' => 'string',
        'subtitle_2' => 'string',
        'subtitle_lang_3' => 'string',
        'subtitle_3' => 'string',
        'release_date' => 'string',
        'release_year' => 'string',
        'imdb_rating' => 'integer',
        'view' => 'integer',
        'status' => 'integer',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }
}
