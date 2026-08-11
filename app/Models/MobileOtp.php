<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MobileOtp extends Model
{

    protected $table = 'mobile_otps';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'mobile' => 'string',
        'otp' => 'integer'
    ];
}
