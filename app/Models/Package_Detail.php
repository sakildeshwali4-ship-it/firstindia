<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package_Detail extends Model
{
    use HasFactory;

    protected $table = 'package_detail';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'package_id' => 'integer',
        'package_key' => 'string',
        'package_value' => 'string',
    ];
}
