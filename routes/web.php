<?php

use App\Http\Controllers\AccessReportController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileFolderController;
use App\Http\Controllers\LiwaFundReportsController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

$baseFileFolders = config('filefolders.baseFolders');

Route::group(['middleware' => ['role:super-admin|admin', 'auth:sanctum', 'verified'], 'prefix' => 'admin'], function () {
    Route::get('users/index', [UsersController::class, 'index'])->name('user.index');
    Route::get('users/list', [UsersController::class, 'getUsersList'])->name('user.list');
    Route::get('users/create', [UsersController::class, 'create'])->name('user.create');
    Route::get('users/edit/{user}', [UsersController::class, 'edit'])->name('user.edit');
    Route::post('users/update/{user}', [UsersController::class, 'update'])->name('user.update');
    Route::post('users/store', [UsersController::class, 'store'])->name('user.store');
    Route::post('users/send-reset-password/{user}', [UsersController::class, 'sendResetPassword'])->name('user.reset-password');
    Route::get('users/{user}/get-password-reset-link', [UsersController::class, 'getPasswordResetLink'])->name('user.get-password-reset-link');
    Route::delete('users/destroy/{user}', [UsersController::class, 'destroy'])->name('user.destroy');
    Route::post('users/{user}/save-folders', [UsersController::class, 'saveUserFolders'])->name('user.save-user-folders-permissions');
    Route::post('users/{user}/save-subfolders', [UsersController::class, 'saveUserSubolders'])->name('user.save-user-subfolders-permissions');

    Route::get('activity/index', [ActivityController::class, 'index'])->name('activity.index');
    Route::get('activity/export', [ActivityController::class, 'exportToExcel'])->name('activity-report.export');
    Route::get('support', [SupportController::class, 'index'])->name('support');

    Route::get('access-report/index', [AccessReportController::class, 'index'])->name('access-report.index');
    Route::get('access-report/export', [AccessReportController::class, 'exportToExcel'])->name('access-report.export');
});

Route::group(['middleware' => ['role:super-admin|admin', 'auth:sanctum', 'verified'], 'prefix' => 'admin'], function () use ($baseFileFolders) {
    Route::get('folders/{folder}/create/{file_folder?}', [FileFolderController::class, 'create'])
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::get('folders/{folder}/edit/{file_folder}', [FileFolderController::class, 'edit'])
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::post('folders/{folder}/update/{file_folder}', [FileFolderController::class, 'update'])
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::post('folders/{folder}/store', [FileFolderController::class, 'store'])
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::delete('folders/{folder}/destroy/{file_folder}', [FileFolderController::class, 'destroy'])
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');

    Route::get('folders/{folder}/add_permission_to_user', [FileFolderController::class, 'addPermissionToUser'])
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::post('folders/{folder}/save_permission_to_user', [FileFolderController::class, 'savePermissionToUser'])
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::get('folders/{folder}/add_subfolder_permission_to_user/{file_folder}', [FileFolderController::class, 'addSubfolderPermissionToUser'])
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::post(
        'folders/{folder}/save_subfolder_permission_to_user/{file_folder}',
        [FileFolderController::class, 'saveSubfolderPermissionToUser']
    )
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::get('folders/{folder}/{file_folder}/investors-access-list', [FileFolderController::class, 'getInvestorsAccess'])
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::get('folders/{folder}/investors-access-list', [FileFolderController::class, 'getInvestorsAccess'])
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    foreach ($baseFileFolders as $baseFileFolder) {
        Route::prefix('folders')->group(function () use ($baseFileFolder) {
            Route::prefix($baseFileFolder)->group(function () use ($baseFileFolder) {
                Route::get('/add_permission_to_user')->name($baseFileFolder . '.add_permission_to_user');
                Route::post('/save_permission_to_user')->name($baseFileFolder . '.save_permission_to_user');
                Route::get('/add_subfolder_permission_to_user/{file_folder}')->name($baseFileFolder . '.add_subfolder_permission_to_user');
                Route::get('/{file_folder}/investors-access-list')->name($baseFileFolder . '.folder.investors-access-list');
                Route::get('/investors-access-list')->name($baseFileFolder . '.folder.investors-basefolder-access-list');
                Route::get('/create')->name($baseFileFolder . '.folder.create');
                Route::get('/edit/{file_folder}')->name($baseFileFolder . '.folder.edit');
                Route::post('/update/{file_folder}')->name($baseFileFolder . '.folder.update');
                Route::post('/store')->name($baseFileFolder . '.folder.store');
                Route::delete('/destroy/{file_folder}')->name($baseFileFolder . '.folder.destroy');
                Route::post('/save_subfolder_permission_to_user/{file_folder}')->name($baseFileFolder . '.save_subfolder_permission_to_user');
            });
        });
    }

    Route::get('analysis-data', [FileFolderController::class, 'create'])
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
});

Route::group(['middleware' => ['role:super-admin|admin', 'auth:sanctum', 'verified'], 'prefix' => 'api/admin'], function () use ($baseFileFolders) {
    Route::get('analysis-data', [ActivityController::class, 'getAnalysisData'])
        ->name('api.admin.activity.analysis-data');
    Route::get('user-activity-data', [ActivityController::class, 'getUserActivityData'])
        ->name('api.admin.activity.user-activity-data');
    Route::get('files-statistic-data', [ActivityController::class, 'getFilesStatisticData'])
        ->name('api.admin.activity.files-statistic-data');
    Route::get('admin-dashboard-export', [ActivityController::class, 'exportAdminDashboardData'])
        ->name('api.admin.activity.export-admin-dashboard-data');
});

Route::group(['middleware' => ['role:super-admin|admin', 'auth:sanctum', 'verified']], function () use ($baseFileFolders) {
    foreach ($baseFileFolders as $baseFileFolder) {
        $controllerName = str_replace(' ', '', ucwords(str_replace('-', ' ', $baseFileFolder))) . 'Controller';
        $controllerClass = 'App\\Http\\Controllers\\' . $controllerName;
        if ($baseFileFolder === 'videos') {
            $controllerClass = VideoController::class;
            Route::post('/folders/' . $baseFileFolder . '/embed/{file_folder?}', [$controllerClass, 'embed'])
                ->name($baseFileFolder . '.embed');
        }

        Route::post('/folders/' . $baseFileFolder . '/store/{file_folder?}', [$controllerClass, 'store'])
            ->name($baseFileFolder . '.store');
        Route::delete('/folders/' . $baseFileFolder . '/destroy/{file}', [$controllerClass, 'destroy'])
            ->name($baseFileFolder . '.destroy');
    }
});

Route::group(['middleware' => ['role:super-admin|admin|investor', 'auth:sanctum', 'verified', 'can_see_folder', 'can_see_subfolder']], function () use ($baseFileFolders) {
    Route::get('/files/view/{file}', [FileController::class, 'viewFile'])->name('file.view');
    Route::get('/files/download/{file}', [FileController::class, 'downloadFile'])->name('file.download');

    Route::group(
        ['middleware' => ['track_folder_action']],
        function () use ($baseFileFolders) {
            Route::get('/', function () {
                return Inertia\Inertia::render('Dashboard');
            })->name('dashboard');

            Route::get('/{folder}', [FileFolderController::class, 'index'])
                ->where('folder', '(' . implode('|', $baseFileFolders) . ')');

            Route::get('folders/{folder}/{file_folder?}', [FileFolderController::class, 'show'])
                ->where('folder', '(' . implode('|', $baseFileFolders) . ')');

            foreach ($baseFileFolders as $baseFileFolder) {
                Route::get('/' . $baseFileFolder, [FileFolderController::class, 'index'])->name($baseFileFolder);
                Route::get('/folders/' . $baseFileFolder . '/{file_folder?}', [FileFolderController::class, 'show'])->name($baseFileFolder . '.folder.index');
            }
        }
    );
});
