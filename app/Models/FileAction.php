<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FileAction;
use App\Models\FileFolder;
use App\Models\FileToFileFolder;

class FileAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'type',
        'initiated_by',
    ];
}
