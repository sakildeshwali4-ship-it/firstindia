<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpisodeReaction extends Model
{
    use HasFactory;

    protected $table = "episode_reactions";

    protected $fillable = [
        'user_id',
        'episode_id',
        'is_like',
        'is_dislike',
        'is_superlike',
        'is_wishlist',
    ];
 
    public function episode()
    {
        return $this->belongsTo(Episode::class, 'episode_id');
    }
 
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
