<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebseriesReaction extends Model
{
    use HasFactory;

    protected $table = "webseries_reactions";

    protected $fillable = [
        'user_id',
        'web_series_id',
        'is_like',
        'is_dislike',
        'is_superlike',
        'is_wishlist',
    ];

    // ✅ Relation with WebSeries
    public function webseries()
    {
        return $this->belongsTo(WebSeries::class, 'web_series_id');
    }

    // ✅ Relation with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
