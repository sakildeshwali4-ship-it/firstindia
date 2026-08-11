<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebSeries extends Model
{
    use HasFactory;

    protected $table = "web_series";

    protected $fillable = [
        'title',
        'slug',
        'description',
        'thumbnail',
        'status',
        'isActive',
        'release_date',
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'release_date' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    public function scopeStreaming($query)
    {
        return $query->where('status', 'streaming');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
    public function episodes()
    {
        return $this->hasMany(Episode::class, "web_series_id");
    }
}
