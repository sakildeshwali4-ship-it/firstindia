<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditionApplication extends Model
{

    protected $table = 'audition_applications';
    protected $guarded = array();

    public function country()
    {
        return $this->belongsTo(Country::class, 'id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'id');
    }

    public function audition()
    {
        return $this->belongsTo(Audition::class, 'id');
    }

}
