<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvView extends Model
{
    use HasFactory;

    protected $table = 'tv_views'; 

    protected $fillable = [
        'user_id',
        'tv_id',
        'type',
        'view_count',
        'last_view_at'
    ];
}
