<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Social_Link extends Model
{
    use HasFactory;

    protected $table = 'tbl_social_link';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'image' => 'string',
        'url' => 'string',
        'status' => 'integer',
    ];
}
