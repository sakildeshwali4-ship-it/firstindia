<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TV_Login extends Model
{
    use HasFactory;

    protected $table = 'tbl_tv_login';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'device_token' => 'string',
        'unique_code' => 'string',
        'user_id' => 'integer',
        'status' => 'integer',
    ];
}
