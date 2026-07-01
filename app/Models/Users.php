<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';
    protected $guarded = array();

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_name' => 'string',
        'name' => 'string',
        'mobile' => 'string',
        'email' => 'string',
        'password' => 'string',
        'image' => 'string',
        'type' => 'integer',
        'status' => 'integer',
        'api_token' => 'string',
        'email_verify_token' => 'string',
        'is_email_verify' => 'string',
    ];
}
