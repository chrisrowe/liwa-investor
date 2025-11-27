<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$baseFileFolders = config('filefolders.baseFolders');

Route::group(['middleware' => ['role:super-admin|admin', 'auth:sanctum', 'verified'], 'prefix' => 'admin'], function () {
    Route::get('users/index', 'App\Http\Controllers\UsersController@index')->name('user.index');
    Route::get('users/list', 'App\Http\Controllers\UsersController@getUsersList')->name('user.list');
    Route::get('users/create', 'App\Http\Controllers\UsersController@create')->name('user.create');
    Route::get('users/edit/{user}', 'App\Http\Controllers\UsersController@edit')->name('user.edit');
    Route::post('users/update/{user}', 'App\Http\Controllers\UsersController@update')->name('user.update');
    Route::post('users/store', 'App\Http\Controllers\UsersController@store')->name('user.store');
    Route::post('users/send-reset-password/{user}', 'App\Http\Controllers\UsersController@sendResetPassword')->name('user.reset-password');
    Route::get('users/{user}/get-password-reset-link', 'App\Http\Controllers\UsersController@getPasswordResetLink')->name('user.get-password-reset-link');
    Route::delete('users/destroy/{user}', 'App\Http\Controllers\UsersController@destroy')->name('user.destroy');
    Route::post('users/{user}/save-folders', 'App\Http\Controllers\UsersController@saveUserFolders')->name('user.save-user-folders-permissions');
    Route::post('users/{user}/save-subfolders', 'App\Http\Controllers\UsersController@saveUserSubolders')->name('user.save-user-subfolders-permissions');

    Route::get('activity/index', 'App\Http\Controllers\ActivityController@index')->name('activity.index');
    Route::get('activity/export', 'App\Http\Controllers\ActivityController@exportToExcel')->name('activity-report.export');
    Route::get('support', 'App\Http\Controllers\SupportController@index')->name('support');

    Route::get('access-report/index', 'App\Http\Controllers\AccessReportController@index')->name('access-report.index');
    Route::get('access-report/export', 'App\Http\Controllers\AccessReportController@exportToExcel')->name('access-report.export');

});

Route::group(['middleware' => ['role:super-admin|admin', 'auth:sanctum', 'verified'], 'prefix' => 'admin'], function () use ($baseFileFolders) {
    Route::get('folders/{folder}/create/{file_folder?}', 'App\Http\Controllers\FileFolderController@create')
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::get('folders/{folder}/edit/{file_folder}', 'App\Http\Controllers\FileFolderController@edit')
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::post('folders/{folder}/update/{file_folder}', 'App\Http\Controllers\FileFolderController@update')
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::post('folders/{folder}/store', 'App\Http\Controllers\FileFolderController@store')
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::delete('folders/{folder}/destroy/{file_folder}', 'App\Http\Controllers\FileFolderController@destroy')
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');

    Route::get('folders/{folder}/add_permission_to_user', 'App\Http\Controllers\FileFolderController@addPermissionToUser')
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::post('folders/{folder}/save_permission_to_user', 'App\Http\Controllers\FileFolderController@savePermissionToUser')
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::get('folders/{folder}/add_subfolder_permission_to_user/{file_folder}', 'App\Http\Controllers\FileFolderController@addSubfolderPermissionToUser')
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::post(
        'folders/{folder}/save_subfolder_permission_to_user/{file_folder}',
        'App\Http\Controllers\FileFolderController@saveSubfolderPermissionToUser'
    )
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::get('folders/{folder}/{file_folder}/investors-access-list', 'App\Http\Controllers\FileFolderController@getInvestorsAccess')
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
    Route::get('folders/{folder}/investors-access-list', 'App\Http\Controllers\FileFolderController@getInvestorsAccess')
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

                /// folder
                Route::post('/save_subfolder_permission_to_user/{file_folder}')->name($baseFileFolder . '.save_subfolder_permission_to_user');
            });
        });
    }

    Route::get('analysis-data', 'App\Http\Controllers\FileFolderController@create')
        ->where('folder', '(' . implode('|', $baseFileFolders) . ')');
});

Route::group(['middleware' => ['role:super-admin|admin', 'auth:sanctum', 'verified'], 'prefix' => 'api/admin'], function () use ($baseFileFolders) {
    Route::get('analysis-data', 'App\Http\Controllers\ActivityController@getAnalysisData')
        ->name('api.admin.activity.analysis-data');
    Route::get('user-activity-data', 'App\Http\Controllers\ActivityController@getUserActivityData')
        ->name('api.admin.activity.user-activity-data');
    Route::get('files-statistic-data', 'App\Http\Controllers\ActivityController@getFilesStatisticData')
        ->name('api.admin.activity.files-statistic-data');
    Route::get('admin-dashboard-export', 'App\Http\Controllers\ActivityController@exportAdminDashboardData')
        ->name('api.admin.activity.export-admin-dashboard-data');
});

Route::group(['middleware' => ['role:super-admin|admin', 'auth:sanctum', 'verified']], function () use ($baseFileFolders) {
    foreach ($baseFileFolders as $baseFileFolder) {
        $controllerName = str_replace(' ', '', ucwords(str_replace('-', ' ', $baseFileFolder))) . 'Controller';
        if ($baseFileFolder === 'videos') {
            $controllerName = 'VideoController';
            Route::post('/folders/' . $baseFileFolder . '/embed/{file_folder?}', 'App\Http\Controllers\\' . $controllerName . '@embed')
                ->name($baseFileFolder . '.embed');
        }

        Route::post('/folders/' . $baseFileFolder . '/store/{file_folder?}', 'App\Http\Controllers\\' . $controllerName . '@store')
            ->name($baseFileFolder . '.store');
        Route::delete('/folders/' . $baseFileFolder . '/destroy/{file}', 'App\Http\Controllers\\' . $controllerName . '@destroy')
            ->name($baseFileFolder . '.destroy');
    }
});

Route::group(['middleware' => ['role:super-admin|admin|investor', 'auth:sanctum', 'verified', 'can_see_folder', 'can_see_subfolder']], function () use ($baseFileFolders) {
    Route::get('/files/view/{file}', 'App\Http\Controllers\FileController@viewFile')->name('file.view');
    Route::get('/files/download/{file}', 'App\Http\Controllers\FileController@downloadFile')->name('file.download');

    Route::group(
        ['middleware' => ['track_folder_action']],
        function () use ($baseFileFolders) {
            Route::get('/', function () {
                return Inertia\Inertia::render('Dashboard');
            })->name('dashboard');

            Route::get('/{folder}', ['App\Http\Controllers\FileFolderController', 'index'])
                ->where('folder', '(' . implode('|', $baseFileFolders) . ')');

            Route::get('folders/{folder}/{file_folder?}', 'App\Http\Controllers\FileFolderController@show')
                ->where('folder', '(' . implode('|', $baseFileFolders) . ')');

            foreach ($baseFileFolders as $baseFileFolder) {
                Route::get('/' . $baseFileFolder)->name($baseFileFolder);
                Route::get('/folders/' . $baseFileFolder . '/{file_folder?}')->name($baseFileFolder . '.folder.index');
            }
        }
    );
});
