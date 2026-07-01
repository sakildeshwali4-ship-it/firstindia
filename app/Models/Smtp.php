<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Smtp extends Model
{
    use HasFactory;

    protected $table = 'smtp_setting';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'protocol' => 'string',
        'host' => 'string',
        'port' => 'string',
        'user' => 'string',
        'pass' => 'string',
        'from_name' => 'string',
        'from_email' => 'string',
        'status' => 'integer',
    ];
}
