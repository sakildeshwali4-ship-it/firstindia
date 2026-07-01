<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video_Watch extends Model
{
    use HasFactory;

    protected $table = 'video_watch';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'video_id' => 'integer',
        'type_id' => 'integer',
        'video_type' => 'integer',
        'stop_time' => 'integer',
        'status' => 'integer',
    ];
}
