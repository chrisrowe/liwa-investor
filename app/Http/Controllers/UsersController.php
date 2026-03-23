<?php

namespace App\Http\Controllers;

use Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Inertia\Inertia;
use App\Models\FileFolder;
use Illuminate\Http\Request;
use App\Models\FileFolderAction;
use App\Mail\InvestorWelcomeEmail;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\SaveAdminRequest;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Permission;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Redirect;
use App\Services\PaginationService;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userData = $this->getUserData($request);

        return Inertia::render('Roles/Users', $userData);
    }

    public function getUsersList(Request $request)
    {
        $userData = $this->getUserData($request);

        return response()->json($userData, 200);
    }

    protected function getUserData(Request $request)
    {
        $usersQuery = User::role(['investor', 'admin']);
        
        if($request->user()->hasAnyRole('super-admin')){
            $usersQuery = User::query();
        }

        if ($request->search) {
            $search = strtolower(trim($request->search));
            $usersQuery = $usersQuery->whereRaw('LOWER(`name`) LIKE ? ',['%' . $search.'%'])
                ->orWhereRaw('LOWER(`email`) LIKE ? ',['%' . $search.'%']);
        }

        $perPage = 50;
        if ($request->perPage && is_numeric($request->perPage)) {
            $perPage = $request->perPage;
        }

        $users = $usersQuery->paginate($perPage)->appends(['perPage' => $perPage]);
        $users->setPath(route('user.index'));

        if ($request->search) {
            $users->appends(['search' => $request->search]);
        }

        $pagintatedUsersLink = PaginationService::paginationLinks($users);

        return [
            'users' => $users,
            'pagintatedUsersLink' => $pagintatedUsersLink,
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $folderCollection = collect(['name', 'subfolders']);
        $allFolders = [];
        $baseFolders = config('filefolders.baseFolders');
        foreach ($baseFolders as $baseFolderName) {
            $folderName = $baseFolderName;
            if ($baseFolderName === 'liwa-fund-reports') {
                $folderName = 'other';
            }
            $permissionName = "view folder {$folderName}";
            $subfolders = FileFolder::where('type', $folderName)
                ->with(['users' => function ($query) {
                    $query->whereHas('roles',function($q){
                        $q->where('name', "investor");
                    });
                }])
                ->get()
                ->toArray();
            $allFolders[] = $folderCollection->combine([$baseFolderName, $subfolders])->all();
        }

        return Inertia::render(
            'Roles/Users/Create',
            [
                'userForEdit' => null,
                'roles' => Role::all(),
                'allFolders' => $allFolders,
                // 'allSubfolders' => $allSubfolders,
                'folders' => [],
                'subfolders' => [],
                'isInvestorUser' => false,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveAdminRequest $request)
    {
        $requestData = $request->all();
        $sendWelcomeEmail = !empty($requestData['send_welcome_email']);
        $password = Str::random();
        $requestData['password'] = Hash::make($password, ['rounds' => 12]);
        $user = User::create($requestData);
        $this->saveUserAdditionalData($user, $requestData);
        if ($user->hasRole('investor') && $sendWelcomeEmail) {
            Mail::to($user)->send(new InvestorWelcomeEmail($user));
        }

        return \Redirect::route('user.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $folderCollection = collect(['name', 'subfolders']);
        $allFolders = [];
        $baseFolders = config('filefolders.baseFolders');
        foreach ($baseFolders as $baseFolderName) {
            $folderName = $baseFolderName;
            if ($baseFolderName === 'liwa-fund-reports') {
                $folderName = 'other';
            }
            $permissionName = "view folder {$folderName}";
            $subfolders = FileFolder::where('type', $folderName)
                ->with(['users' => function ($query) {
                    $query->whereHas('roles',function($q){
                        $q->where('name', "investor");
                    });
                }])
                ->get()
                ->toArray();
            $allFolders[] = $folderCollection->combine([$baseFolderName, $subfolders])->all();
        }

        // $allFolders = config('filefolders.baseFolders');
        // $allSubfolders = FileFolder::all();
        $folders = $user->baseFileFolders;
        foreach ($folders as &$folder) {
            $folder = strtolower(str_replace(' ', '-', $folder));
            if ($folder === 'other') {
                $folder = 'liwa-fund-reports';
            }
        }
        $subfolders = $user->fileFolders;

        // dd($allFolders);

        return Inertia::render('Roles/Users/Create', [
            'userForEdit' => $user,
            'roles' => Role::all(),
            'allFolders' => $allFolders,
            // 'allFolders' => $allFolders,
            // 'allSubfolders' => $allSubfolders,
            'folders' => $folders,
            'subfolders' => $subfolders,
            'isInvestorUser' => $user->hasRole('investor'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(SaveAdminRequest $request, User $user)
    {
        $requestData = $request->all();
        $user->update($requestData);
        $this->saveUserAdditionalData($user, $requestData);

        return \Redirect::route('user.edit', [$user->id]);
    }

    public function saveUserFolders(Request $request, User $user)
    {
        $requestData = $request->all();
        $folders = isset($requestData['folders']) ? $requestData['folders'] : [];
        $subfolders = isset($requestData['subfolders']) ? $requestData['subfolders'] : [];
        $this->saveFoldersPermissions($folders, $user);
        $this->saveSubfolderFoldersPermissions($subfolders, $user);

        return redirect()->back();
    }

    public function saveUserSubolders(Request $request, User $user)
    {
        $requestData = $request->all();
        $subfolders = isset($requestData['subfolders']) ? $requestData['subfolders'] : [];
        $this->saveSubfolderFoldersPermissions($subfolders, $user);

        return redirect()->back();
    }

    protected function saveUserAdditionalData($user, $dataForSave)
    {
        if (!empty($dataForSave['role'])) {
            if (!$user->hasRole($dataForSave['role'])) {
                $roles = $user->getRoleNames();
                foreach ($roles as $role) {
                    $user->removeRole($role);
                }
                $user->assignRole($dataForSave['role']);
            }
        }
        $folders = isset($dataForSave['folders']) ? $dataForSave['folders'] : [];
        $subfolders = isset($dataForSave['subfolders']) ? $dataForSave['subfolders'] : [];
        $this->saveFoldersPermissions($folders, $user);
        $this->saveSubfolderFoldersPermissions($subfolders, $user);
    }

    protected function saveFoldersPermissions($folders, $user)
    {
        if (empty($folders)) {
            $userPermissions = $user->permissions->pluck('name')->toArray();
            if ($userPermissions) {
                $user->revokePermissionTo($userPermissions);
            }

            return;
        }
        $userPermissions = $user->getPermissionNames()->toArray();
        foreach ($folders as $requestFolder) {
            if ($requestFolder === 'liwa-fund-reports') {
                $requestFolder = 'other';
            }
            $permissionName = 'view folder ' . $requestFolder;
            if (in_array($permissionName, $userPermissions)) {
                continue;
            }
            $permission = Permission::where(['name' => $permissionName])->first();
            if (!$permission) {
                $permission = Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
            }
            $user->givePermissionTo($permissionName);
            FileFolderAction::create(
                [
                    'action' => 'add_access',
                    'folder' => $requestFolder,
                    'subfolder_id' => null,
                    'initiated_by' => Auth::user()->id,
                    'action_data' => ['user_id' => $user->id],
                ]
            );
        }
        foreach ($userPermissions as $permissionName) {
            $folder = str_replace('view folder ', '', $permissionName);
            if ($folder === 'other') {
                $folder = 'liwa-fund-reports';
            }
            if (in_array($folder, $folders)) {
                continue;
            }
            if ($folder === 'liwa-fund-reports') {
                $folder = 'other';
            }
            $user->revokePermissionTo($permissionName);
            FileFolderAction::create(
                [
                    'action' => 'remove_access',
                    'folder' => $folder,
                    'subfolder_id' => null,
                    'initiated_by' => Auth::user()->id,
                    'action_data' => ['user_id' => $user->id],
                ]
            );
        }
    }

    protected function saveSubfolderFoldersPermissions($subfolders, $user)
    {
        if (empty($subfolders)) {
            $user->fileFolders()->detach();
            return;
        }
        $currentFileFolders = $user->fileFolders()->get();
        $currentFileFolderIds = $user->fileFolders()->get()->pluck('id')->toArray();
        foreach ($subfolders as $requestSubfolder) {
            if (in_array($requestSubfolder, $currentFileFolderIds)) {
                continue;
            }
            $subfolder = FileFolder::find($requestSubfolder);
            if (!$subfolder) {
                continue;
            }
            $user->fileFolders()->attach($subfolder->id);
            FileFolderAction::create(
                [
                    'action' => 'add_access',
                    'folder' => $subfolder->type,
                    'subfolder_id' => $subfolder->id,
                    'initiated_by' => Auth::user()->id,
                    'action_data' => ['user_id' => $user->id],
                ]
            );
        }
        foreach ($currentFileFolders as $subfolder) {
            if (in_array($subfolder->id, $subfolders)) {
                continue;
            }
            $user->fileFolders()->detach($subfolder->id);
            FileFolderAction::create(
                [
                    'action' => 'remove_access',
                    'folder' => $subfolder->type,
                    'subfolder_id' => $subfolder->id,
                    'initiated_by' => Auth::user()->id,
                    'action_data' => ['user_id' => $user->id],
                ]
            );
        }
    }

    public function sendResetPassword(Request $request, User $user)
    {
        $passwordBroker = Password::broker(config('fortify.passwords'));

        $status = $passwordBroker->sendResetLink(['email' => $user->email]);

        return \Redirect::route('user.edit', [$user->id]);
    }

    public function getPasswordResetLink(Request $request, User $user)
    {
        $passwordBroker = Password::broker(config('fortify.passwords'));

        $token = $passwordBroker->createToken($user);

        return route('password.reset', [
            'token' => $token
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return \Redirect::route('user.index');
    }
}
