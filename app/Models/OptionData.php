<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptionData extends Model
{
    protected $table = "options_data";

    protected $fillable = [
        'type',
        'image',
        'url',
        'status'
    ];
}