<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'type',
        'created_by',
        'extension',
    ];

    protected $appends = ['url', 'views'];

    protected $with = ['video'];

    public function getLastOpenedAtAttribute()
    {
    	return $this->lastOpenedAction->created_at;
    }

    public function lastOpenedAction()
    {
    	return $this->actions()
    		->where('type', 'view')
    		->last();
    }


    public function actions()
    {
    	return $this->hasMany(FileAction::class);
    }

    public function viewActions()
    {
    	return $this->actions()
    		->where('type', 'view');
    }

    public function viewedOrDownloaded()
    {
    	return $this->actions()
    		->whereIn('type', ['view', 'download']);
    }

    public function folder()
    {
    	return $this->belongsToMany(
            FileFolder::class,
            'file_to_file_folders',
            'file_id',
            'folder_id'
        );
    }

    public function getViewsAttribute(){
        return $this->viewedOrDownloaded->count();
    }

    public function getUrlAttribute()
    {
        if (!$this->path) {
            return '';
        }

        return Storage::url($this->path);
    }

    public function video()
    {
        return $this->hasOne(
            FileVideo::class,
            'file_id',
            'id'
        );
    }
}
