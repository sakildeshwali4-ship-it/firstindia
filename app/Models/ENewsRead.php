<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ENewsRead extends Model
{
    protected $table = 'enews_reads';
    protected $fillable = ['user_id', 'enews_id'];
 
}
