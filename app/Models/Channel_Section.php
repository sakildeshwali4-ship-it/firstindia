<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel_Section extends Model
{
    use HasFactory;

    protected $table = 'channel_section';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'channel_id' => 'string',
        'type_id' => 'integer',
        'video_type' => 'integer',
        'section_type' => 'integer',
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

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }
}
