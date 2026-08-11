<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    protected $guarded = array();
    protected $fillable = [
        'title',
        'type',
        'media_url',
        'media_type',
        'click_url',
        'start_after_seconds',
        'repeat_every_seconds',
        'duration_seconds',
        'skippable_after_seconds',
        'priority',
        'active',
        'start_date',
        'end_date'
    ];
}