<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $table = "seasons";

    protected $fillable = [
        'web_series_id',
        'season_number',
        'thumbnail',
        'video',
        'title',
        'meta_desc',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    public function webSeries()
    {
        return $this->belongsTo(WebSeries::class, 'web_series_id');
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }

    public function trailers()
    {
        return $this->hasMany(SeasonTrailer::class);
    }

}
