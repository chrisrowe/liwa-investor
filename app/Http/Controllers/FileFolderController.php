<?php

namespace App\Http\Controllers;

use App\Models\FileFolder;
use App\Models\FileFolderAction;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\PaginationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Spatie\Permission\Models\Permission;

class FileFolderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $baseFolder)
    {
        $folderType = $baseFolder;
        if ($baseFolder === 'liwa-fund-reports') {
            $folderType = 'other';
        }
        $foldersQuery = FileFolder::doesntHave('parentFolder')
            ->where('type', $folderType)
            ->orderBy('name');
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'super-admin']) && $user->hasRole(['investor'])) {
            $foldersQuery = $foldersQuery->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        }
        $folders = $foldersQuery->paginate(50, ['*'], 'folderPage')->appends(['page' => $request->page ?: 1]);
        $paginatedLinks = PaginationService::paginationLinks($folders);

        $files = File::where('type', $folderType)
            ->doesntHave('folder')
            ->orderBy('created_at', 'DESC')
            ->paginate(50)
            ->appends(['folderPage' => $request->folderPage?:1]);

        $paginatedFileLinks = PaginationService::paginationLinks($files);

        $pageTitle = $baseFolder;
        if ($baseFolder === 'liwa-fund-reports') {
            $pageTitle = 'Liwa Fund Reports';
        }

        return Inertia::render('Folders/Index', [
            'folders' => $folders,
            'title' => ucfirst($pageTitle),
            'paginatedLinks' => $paginatedLinks,
            'folderType' => $baseFolder,
            'files' => $files,
            'paginatedFileLinks' => $paginatedFileLinks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($baseFolder, FileFolder $fileFolder = null)
    {
        return Inertia::render('Folders/CreateEdit', [
            'folderType' => $baseFolder,
            'folder' => $fileFolder,
            'createNew' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($baseFolder, Request $request)
    {
        $requestData = $request->all();
        $baseFolderForSave = $baseFolder;
        $folderType = $baseFolderForSave;
        if ($folderType === 'liwa-fund-reports') {
            $folderType = 'other';
        }
        $requestData['type'] = $folderType;
        $requestData['created_by'] = Auth::user()->id;

        $fileFolder = FileFolder::create($requestData);

        if (!empty($requestData['parent_folder_id'])) {
            return \Redirect::route($baseFolder . '.folder.index', $requestData['parent_folder_id']);
        }

        return \Redirect::route($baseFolder);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FileFolder  $fileFolder
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $baseFolder, FileFolder $fileFolder)
    {
        $user = Auth::user();
        $folderType = $baseFolder;
        if ($baseFolder === 'liwa-fund-reports') {
            $folderType = 'other';
        }

        if (!$user->hasRole(['admin', 'super-admin']) && $user->hasRole(['investor']) ) {
            $isHaveUserPermission = (boolean) User::where('id', $user->id)
                ->whereHas('fileFolders', function ($query) use ($fileFolder) {
                    $fileFolderId = $fileFolder->parent_folder_id ? $fileFolder->parent_folder_id : $fileFolder->id;
                    $query->where('file_folders.id', $fileFolderId);
                })
                ->first();
            if (!$isHaveUserPermission) {
                abort(403);
            }
        }

        $files = File::where('type', $folderType)
            ->orderBy('created_at', 'DESC')
            ->whereHas('folder', function($q) use ($fileFolder) {
                $q->where('file_folders.id', $fileFolder->id);
            })
            ->paginate(10)
            ->appends(['folderPage' => $request->folderPage?:1]);
        $paginatedLinks = PaginationService::paginationLinks($files);

        $folders = $fileFolder->folders()->paginate(10, ['*'], 'folderPage')->appends(['page' => $request->page ?: 1]);
        $paginatedFolderLinks = PaginationService::paginationLinks($folders);

        return Inertia::render('Folders/Show', [
            'folders' => $folders,
            'files' => $files,
            'paginatedLinks' => $paginatedLinks,
            'folderType' => $baseFolder,
            'title' => ucfirst($baseFolder),
            'fileFolder' => $fileFolder,
            'paginatedFolderLinks' => $paginatedFolderLinks,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FileFolder  $fileFolder
     * @return \Illuminate\Http\Response
     */
    public function edit($baseFolder, FileFolder $fileFolder)
    {
        $allInvestors = User::whereHas('roles', function($q){
            $q->where('name', 'investor');
        })
        ->get();

        return Inertia::render('Folders/CreateEdit', [
            'folderType' => $baseFolder,
            'folder' => $fileFolder,
            'allInvestors' => $allInvestors,
            'createNew' => false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FileFolder  $fileFolder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $baseFolder, FileFolder $fileFolder)
    {
        $requestData = $request->all();
        $fileFolder->update($requestData);

        $folderType = $baseFolder;
        if ($baseFolder === 'liwa-fund-reports') {
            $folderType = 'other';
        }
        if (!empty($requestData['investors'])) {
            $usersWithoutAccess = User::whereHas('roles',function($q){
                $q->where('name', "investor");
            })
                ->whereHas('fileFolders', function ($query) use ($fileFolder) {
                    $query->where('file_folders.id', $fileFolder->id);
                })
                ->whereNotIn('users.id', $requestData['investors'])
                ->get()
                ->all();
            $usersForSavingAccess = User::whereHas('roles',function($q){
                $q->where('name', "investor");
            })
                ->whereDoesntHave('fileFolders', function ($query) use ($fileFolder) {
                    $query->where('file_folders.id', $fileFolder->id);
                })
                ->whereIn('users.id', $requestData['investors'])
                ->get()
                ->all();
            foreach ($usersWithoutAccess as $user) {
                $fileFolder->users()->detach([$user->id]);
                FileFolderAction::create(
                    [
                        'action' => 'remove_access',
                        'folder' => $folderType,
                        'subfolder_id' => $fileFolder->id,
                        'initiated_by' => Auth::user()->id,
                        'action_data' => ['user_id' => $user->id],
                    ]
                );
            }
            foreach ($usersForSavingAccess as $user) {
                $fileFolder->users()->attach([$user->id]);
                FileFolderAction::create(
                    [
                        'action' => 'add_access',
                        'folder' => $folderType,
                        'subfolder_id' => $fileFolder->id,
                        'initiated_by' => Auth::user()->id,
                        'action_data' => ['user_id' => $user->id],
                    ]
                );
            }
        }

        return \Redirect::route($baseFolder . '.folder.edit', [$fileFolder->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FileFolder  $fileFolder
     * @return \Illuminate\Http\Response
     */
    public function addSubfolderPermissionToUser($baseFolder, FileFolder $fileFolder)
    {
        $usersWithoutAccessQuery = User::whereHas('roles',function($q){
                $q->where('name', "investor");
            })
            ->whereDoesntHave('fileFolders', function ($query) use ($fileFolder) {
                $query->where('file_folders.id', $fileFolder->id);
            });

        $usersWithoutAccess = $usersWithoutAccessQuery->get();

        return Inertia::render('Folders/AddSubfolderPermissionToUser', [
            'folderType' => $baseFolder,
            'folder' => $fileFolder,
            'allInvestors' => $usersWithoutAccess,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FileFolder  $fileFolder
     * @return \Illuminate\Http\Response
     */
    public function saveSubfolderPermissionToUser(Request $request, $baseFolder, FileFolder $fileFolder)
    {
        $folderType = $baseFolder;
        if ($baseFolder === 'liwa-fund-reports') {
            $folderType = 'other';
        }
        $requestData = $request->all();
        if (!empty($requestData['investors'])) {
            $usersWithoutAccess = User::whereHas('roles',function($q){
                $q->where('name', "investor");
            })
                ->whereHas('fileFolders', function ($query) use ($fileFolder) {
                    $query->where('file_folders.id', $fileFolder->id);
                })
                ->whereNotIn('users.id', $requestData['investors'])
                ->get()
                ->all();
            $usersForSavingAccess = User::whereHas('roles',function($q){
                $q->where('name', "investor");
            })
                ->whereDoesntHave('fileFolders', function ($query) use ($fileFolder) {
                    $query->where('file_folders.id', $fileFolder->id);
                })
                ->whereIn('users.id', $requestData['investors'])
                ->get()
                ->all();
            foreach ($usersWithoutAccess as $user) {
                $fileFolder->users()->detach([$user->id]);
                FileFolderAction::create(
                    [
                        'action' => 'remove_access',
                        'folder' => $folderType,
                        'subfolder_id' => $fileFolder->id,
                        'initiated_by' => Auth::user()->id,
                        'action_data' => ['user_id' => $user->id],
                    ]
                );
            }
            foreach ($usersForSavingAccess as $user) {
                $fileFolder->users()->attach([$user->id]);
                FileFolderAction::create(
                    [
                        'action' => 'add_access',
                        'folder' => $folderType,
                        'subfolder_id' => $fileFolder->id,
                        'initiated_by' => Auth::user()->id,
                        'action_data' => ['user_id' => $user->id],
                    ]
                );
            }
        } else {
            $usersWithoutAccess = User::whereHas('roles',function($q){
                $q->where('name', "investor");
            })
                ->whereHas('fileFolders', function ($query) use ($fileFolder) {
                    $query->where('file_folders.id', $fileFolder->id);
                })
                ->get()
                ->all();
            foreach ($usersWithoutAccess as $user) {
                $fileFolder->users()->detach([$user->id]);
                FileFolderAction::create(
                    [
                        'action' => 'remove_access',
                        'folder' => $folderType,
                        'subfolder_id' => $fileFolder->id,
                        'initiated_by' => Auth::user()->id,
                        'action_data' => ['user_id' => $user->id],
                    ]
                );
            }
        }

        // return redirect()->back();
        //return \Redirect::route($baseFolder . '.add_subfolder_permission_to_user', [$fileFolder->id]);
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FileFolder  $fileFolder
     * @return \Illuminate\Http\Response
     */
    public function destroySubfolderPermissionToUser(Request $request, $baseFolder, FileFolder $fileFolder)
    {
        if (!empty($request->investors)) {
            $fileFolder->users()->detach($request->investors);
        }

        // return redirect()->back();
        //return \Redirect::route($baseFolder . '.add_subfolder_permission_to_user', [$fileFolder->id]);
    }

    public function getInvestorsAccess(Request $request, $folder, FileFolder $fileFolder = null)
    {
        $usersWithoutAccessToSubfolder = [];
        $usersWithAccessToSubfolder = [];
        $folderType = $folder;
        if ($folder === 'liwa-fund-reports') {
            $folderType = 'other';
        }
        $usersWithoutBaseFolderAccess = User::whereHas('roles',function($q){
            $q->where('name', "investor");
        })
        ->whereDoesntHave('permissions',function($q) use ($folderType) {
            $q->where('name', "view folder " . $folderType);
        })
        ->get();
        $usersWithBaseFolderAccess = User::whereHas('permissions',function($q) use ($folderType) {
            $q->where('name', "view folder " . $folderType);
        })
        ->get();

        if ($fileFolder) {
            $usersWithoutAccessQuery = User::whereHas('roles',function($q){
                    $q->where('name', "investor");
                })
                ->whereDoesntHave('fileFolders', function ($query) use ($fileFolder) {
                    $query->where('file_folders.id', $fileFolder->id);
                });

            if ($request->searchQuery) {
                $usersWithoutAccessQuery = $usersWithoutAccessQuery
                    ->where('name', 'like', '%' . $request->searchQuery . '%')
                    ->get();
            }

            $usersWithoutAccessToSubfolder = $usersWithoutAccessQuery->get();
            $usersWithAccessToSubfolder = $fileFolder->users()
                ->whereHas('roles',function($q){
                    $q->where('name', "investor");
                })
                ->get();
        }

        return response()->json(
            [
                'success' => true,
                'usersWithoutAccess' => $usersWithoutAccessToSubfolder,
                'usersWithAccess' => $usersWithAccessToSubfolder,
                'usersWithoutBaseFolderAccess' => $usersWithoutBaseFolderAccess,
                'usersWithBaseFolderAccess' => $usersWithBaseFolderAccess,
            ],
            200
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FileFolder  $fileFolder
     * @return \Illuminate\Http\Response
     */
    public function addPermissionToUser($baseFolder)
    {
        $folderType = $baseFolder;
        if ($baseFolder === 'liwa-fund-reports') {
            $folderType = 'other';
        }
        $allInvestors = User::whereHas('roles',function($q){
            $q->where('name', "investor");
        })
        ->get();
        $investorsWithPermissions = User::whereHas('permissions',function($q) use ($folderType) {
            $q->where('name', "view folder " . $folderType);
        })
        ->get();

        return Inertia::render('Folders/AddPermissionToUser', [
            'folderType' => $baseFolder,
            'allInvestors' => $allInvestors,
            'investorsWithPermissions' => $investorsWithPermissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FileFolder  $fileFolder
     * @return \Illuminate\Http\Response
     */
    public function savePermissionToUser(Request $request, $baseFolder)
    {
        $folderType = $baseFolder;
        if ($baseFolder === 'liwa-fund-reports') {
            $folderType = 'other';
        }
        $permissionName = "view folder " . $folderType;
        $permission = Permission::where(['name' => $permissionName])->first();
        if (!$permission) {
            $permission = Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
        }
        $requestData = $request->all();

        if (!empty($requestData['investors'])) {
            $usersWithoutPermission = User::whereHas('permissions',function($q) use ($permissionName) {
                $q->where('name', $permissionName);
            })
                ->whereNotIn('id', $requestData['investors'])
                ->get();
            $usersForSavingPermission = User::whereDoesntHave('permissions',function($q) use ($permissionName) {
                $q->where('name', $permissionName);
            })
                ->whereIn('id', $requestData['investors'])
                ->get();
            foreach ($usersWithoutPermission as $user) {
                $user->revokePermissionTo($permissionName);
                FileFolderAction::create(
                    [
                        'action' => 'remove_access',
                        'folder' => $folderType,
                        'subfolder_id' => null,
                        'initiated_by' => Auth::user()->id,
                        'action_data' => ['user_id' => $user->id],
                    ]
                );
            }
            foreach ($usersForSavingPermission as $user) {
                $user->givePermissionTo($permissionName);
                FileFolderAction::create(
                    [
                        'action' => 'add_access',
                        'folder' => $folderType,
                        'subfolder_id' => null,
                        'initiated_by' => Auth::user()->id,
                        'action_data' => ['user_id' => $user->id],
                    ]
                );
            }
        } else {
            $usersWithPermission = User::whereHas('permissions',function($q) use ($permissionName) {
                $q->where('name', $permissionName);
            })
                ->get();
            foreach ($usersWithPermission as $user) {
                $user->revokePermissionTo($permissionName);
                FileFolderAction::create(
                    [
                        'action' => 'remove_access',
                        'folder' => $folderType,
                        'subfolder_id' => null,
                        'initiated_by' => Auth::user()->id,
                        'action_data' => ['user_id' => $user->id],
                    ]
                );
            }
        }

        return \Redirect::route($baseFolder);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FileFolder  $fileFolder
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $folder, FileFolder $fileFolder)
    {
        foreach ($fileFolder->folders as $subfolder) {
            $subfolder->delete();
        }
        $fileFolder->delete();

        return redirect()->back();
    }
}
