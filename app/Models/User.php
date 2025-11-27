<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\FileFolder;
use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    protected $with = [
        'roles',
    ];

    public function fileFolders()
    {
        return $this->belongsToMany(
            FileFolder::class,
            'user_to_folder',
            'user_id',
            'folder_id'
        );
    }

    public function getBaseFileFoldersAttribute()
    {
        $baseFolders = config('filefolders.baseFolders');
        $folderViewPermissions = [];
        foreach ($baseFolders as $value) {
            if ($value === 'liwa-fund-reports') {
                $value = 'other';
            }
            $folderViewPermissions[] = "view folder {$value}";
        }
        if (!$folderViewPermissions) {
            return [];
        }
        $permissions = $this->permissions()
            ->whereIn('name', $folderViewPermissions)
            ->get()
            ->all();
        $folders = [];
        foreach ($permissions as $permission) {
            $folders[] = ucfirst(str_replace('view folder ', '', $permission->name));
        }

        return $folders;
    }

    public function haveAccessToSubfolder(FileFolder $subfolder = null): bool
    {
        if (!$subfolder) {
            return true;
        }
        if ($this->hasRole(['admin', 'super-admin'])) {
            return true;
        }
        return User::whereHas('fileFolders', function($q) use ($subfolder) {
                $fileFolderId = $subfolder->parent_folder_id ? $subfolder->parent_folder_id : $subfolder->id;
                $q->where('file_folders.id', $fileFolderId);
            })
            ->where('id', $this->id)
            ->exists();
    }

    public function haveAccessToFolder(string $folderName = null): bool
    {
        if ($this->hasRole(['admin', 'super-admin'])) {
            return true;
        }
        if (!$folderName) {
            return true;
        }
        if ($folderName === 'liwa-fund-reports') {
            $folderName = 'other';
        }
        $folderViewPermissionName = 'view folder ' . $folderName;

        return $this->can($folderViewPermissionName);
    }
}
