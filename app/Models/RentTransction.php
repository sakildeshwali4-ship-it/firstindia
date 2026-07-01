<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentTransction extends Model
{
    use HasFactory;

    protected $table = 'rent_transction';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'unique_id' => 'string',
        'user_id' => 'integer',
        'type_id' => 'integer',
        'video_type' => 'integer',
        'video_id' => 'integer',
        'price' => 'integer',
        'payment_id' => 'string',
        'description' => 'string',
        'currency_code' => 'string',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
    public static function getVideoName($video_id, $type)
    {
        if ($type == 1) {
            return Video::where('id', $video_id)->pluck('name')->first();
        } else {
            return TVShow::where('id', $video_id)->pluck('name')->first();
        }
    }
}
