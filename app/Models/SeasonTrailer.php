<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonTrailer extends Model
{
    protected $fillable = [
        'season_id',
        'title',
        'thumbnail',
        'landscape',
        'video_url',
        'position',
        'status'
    ];
}