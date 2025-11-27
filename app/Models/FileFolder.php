<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'created_by',
        'parent_folder_id',
    ];

    protected $with = [
    	'users',
        'parentFolder'
    ];

    public function files()
    {
    	return $this->belongsToMany(
            File::class,
            'file_to_file_folders',
            'folder_id',
            'file_id'
        );



    }

    public function users()
    {
    	return $this->belongsToMany(
            User::class,
            'user_to_folder',
            'folder_id',
            'user_id'
        );
    }

    public function folders()
    {
        return $this->hasMany(
            self::class,
            'parent_folder_id',
            'id'
        );
    }

    public function parentFolder()
    {
        return $this->hasOne(
            self::class,
            'id',
            'parent_folder_id'
        );
    }

    public function delete()
    {
        $files = $this->files;
        $this->files()->detach();
        foreach ($files as $file) {
            $file->delete();
        }
        
        parent::delete();
    }
}
