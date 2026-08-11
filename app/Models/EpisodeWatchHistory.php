<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpisodeWatchHistory extends Model
{
    protected $table = "episode_watch_histories";

    protected $fillable = [
        "user_id",
        "episode_id",
        "watch_progress",
        "is_watched"
    ];
}