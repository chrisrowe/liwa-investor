<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FileFolder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use DB;
use App\Services\PaginationService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AccessReportExport\AccessReportExport;

class AccessReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $folders = $this->getReportsFolders();

        return Inertia::render('AccessReport', [
            'folders' => $folders,
        ]);
    }

    public function exportToExcel()
    {
        $folders = $this->getReportsFolders();
        $date = date('Y:m:d h:i:s');

        return Excel::download(new AccessReportExport($folders), "Acces Reports - {$date}.xlsx");
    }

    protected function getReportsFolders()
    {
        $folderCollection = collect(['name', 'investors', 'subfolders']);
        $folders = [];
        $baseFolders = config('filefolders.baseFolders');
        foreach ($baseFolders as $baseFolderName) {
            $folderName = $baseFolderName;
            if ($baseFolderName === 'liwa-fund-reports') {
                $folderName = 'other';
            }
            $permissionName = "view folder {$folderName}";
            $users = User::whereHas('permissions', function($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
                ->get()
                ->toArray();
            $subfolders = FileFolder::where('type', $folderName)
                ->with(['users' => function ($query) {
                    $query->whereHas('roles',function($q){
                        $q->where('name', "investor");
                    });
                }])
                ->get()
                ->toArray();
            $folders[] = $folderCollection->combine([$baseFolderName, $users, $subfolders])->all();
        }

        return $folders;
    }
}
