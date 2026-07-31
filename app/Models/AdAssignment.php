<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdAssignment extends Model
{
    protected $fillable = [
        'ad_id',
        'assignable_type',
        'assignable_id',
        'ad_position',
        'sort_order',
        'active',
    ];

    public function ad()
    {
        return $this->belongsTo(Ads::class, 'ad_id');
    }

    public function video()
    {
        return $this->belongsTo(Video::class, 'assignable_id');
    }

    public function liveTv()
    {
        return $this->belongsTo(LiveTv::class, 'assignable_id');
    }
}
