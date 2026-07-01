<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'tbl_coupon';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'unique_id' => 'string',
        'name' => 'string',
        'amount_type' => 'integer',
        'price' => 'string',
        'is_use' => 'integer',
        'status' => 'integer',
    ];
}
