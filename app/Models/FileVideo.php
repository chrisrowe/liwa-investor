<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class FileVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'embeded',
    ];
}
