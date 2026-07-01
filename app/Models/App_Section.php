<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App_Section extends Model
{
    use HasFactory;

    protected $table = 'app_section';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'is_home_screen' => 'integer',
        'type_id' => 'integer',
        'video_type' => 'integer',
        'upcoming_type' => 'integer',
        'status' => 'integer',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
