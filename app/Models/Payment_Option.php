<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment_Option extends Model
{
    use HasFactory;

    protected $table = 'payment_option';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'visibility' => 'string',
        'is_live' => 'string',
        'live_key_1' => 'string',
        'live_key_2' => 'string',
        'live_key_3' => 'string',
        'test_key_1' => 'string',
        'test_key_2' => 'string',
        'test_key_3' => 'string',
    ];
}
