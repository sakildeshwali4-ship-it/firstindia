<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TVShowVideo extends Model
{
    use HasFactory;

    protected $table = 'tv_show_video';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'show_id' => 'integer',
        'session_id' => 'integer',
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
        'subtitle_type' => 'string',
        'subtitle_lang_1' => 'string',
        'subtitle_1' => 'string',
        'subtitle_lang_2' => 'string',
        'subtitle_2' => 'string',
        'subtitle_lang_3' => 'string',
        'subtitle_3' => 'string',
        'view' => 'integer',
        'sortable' => 'integer',
        'status' => 'integer',
    ];

    public function show()
    {
        return $this->belongsTo(TVShow::class, 'show_id');
    }
    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}
