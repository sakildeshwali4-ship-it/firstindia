<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TVShow extends Model
{
    use HasFactory;

    protected $table = 'tv_show';
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
        'trailer_type' => 'string',
        'trailer_url' => 'string',
        'description' => 'string',
        'is_premium' => 'integer',
        'is_title' => 'string',
        'view' => 'integer',
        'imdb_rating' => 'integer',
        'status' => 'integer',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }
}
