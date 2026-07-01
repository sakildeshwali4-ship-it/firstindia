<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentVideo extends Model
{
    use HasFactory;

    protected $table = 'rent_video';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'type_id' => 'integer',
        'video_type' => 'integer',
        'video_id' => 'integer',
        'price' => 'integer',
        'time' => 'string',
        'type' => 'string',
        'status' => 'integer',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }
    public function tvshow()
    {
        return $this->belongsTo(TVShow::class, 'video_id');
    }
}
