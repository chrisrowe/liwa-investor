<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileFolderAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'folder',
        'subfolder_id',
        'initiated_by',
        'action_data',
    ];

    protected $casts = [
        'action_data' => 'json'
    ];
}
