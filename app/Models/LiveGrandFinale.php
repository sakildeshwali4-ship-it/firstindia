<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveGrandFinale extends Model
{

    protected $table = 'live_grand_finale';
    protected $guarded = array();

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

}
