<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpisodeView extends Model
{
    use HasFactory;

    protected $table = "episode_views";

    protected $fillable = [
        "user_id",
        "episode_id",
        "watched_duration",
        "counted",
    ];
 
    public function episode()
    {
        return $this->belongsTo(Episode::class, "episode_id");
    }
 
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
