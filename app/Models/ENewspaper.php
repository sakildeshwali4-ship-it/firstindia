<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ENewspaper extends Model
{
    protected $fillable = [
        'type', 'date', 'pdf_file', 'highlight_image', 'status'
    ];
}
