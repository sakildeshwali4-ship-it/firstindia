<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transction extends Model
{
    use HasFactory;

    protected $table = 'transaction';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'unique_id' => 'string',
        'package_id' => 'integer',
        'description' => 'string',
        'amount' => 'string',
        'payment_id' => 'string',
        'currency_code' => 'string',
        'status' => 'integer',
        'is_delete' => 'integer',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}
