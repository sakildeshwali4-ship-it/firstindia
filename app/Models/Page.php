<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $table = 'page';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'page_name' => 'string',
        'title' => 'string',
        'description' => 'string',
        'page_subtitle' => 'string',
        'icon' => 'string',
        'status' => 'integer',
    ];
}
