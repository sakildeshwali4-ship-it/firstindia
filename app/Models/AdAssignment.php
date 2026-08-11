<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdAssignment extends Model
{
    use HasFactory;

    protected $table = 'ad_assignments';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'ad_id' => 'integer',
        'assignable_type' => 'string',
        'assignable_id' => 'integer',
        'ad_position' => 'string',
        'sort_order' => 'integer',
        'active' => 'integer',
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
