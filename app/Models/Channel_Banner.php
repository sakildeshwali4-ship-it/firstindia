<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel_Banner extends Model
{
    use HasFactory;

    protected $table = 'channel_banner';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'image' => 'string',
        'order_no' => 'integer',
        'status' => 'integer',
    ];
}
