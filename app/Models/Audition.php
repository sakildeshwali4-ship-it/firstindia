<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audition extends Model
{

    protected $table = 'auditions';
    protected $guarded = array();
	
	public function city()
    {
        return $this->belongsTo(City::class);
    }

}
