<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveTv extends Model
{
    use HasFactory;
    protected $fillable = ['name','image','dialog_image','url'];

    // public function getImageAttribute($value)
    // {
    //     return $value ? asset('storage/' . $value) : null;
    // }

    // public function getDialogImageAttribute($value)
    // {
    //     return $value ? asset('storage/' . $value) : null;
    // }
    public function getImageAttribute($value)
    {
        return $value ? asset('/' . $value) : null;
    }

    public function getDialogImageAttribute($value)
    {
        return $value ? asset('/' . $value) : null;
    }
}
