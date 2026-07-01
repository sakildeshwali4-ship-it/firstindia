<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ENewsVisit extends Model
{
    protected $table = 'enews_visits';
    protected $fillable = ['user_id', 'date'];
 
}
