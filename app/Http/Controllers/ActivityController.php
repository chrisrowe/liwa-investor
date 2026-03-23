<?php

namespace App\Http\Controllers;

use App\Exports\ActivityReportExport\ActivityReportExport;
use App\Models\File;
use App\Models\FileFolderAction;
use App\Models\FileAction;
use App\Models\User;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\PaginationService;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\AdminDashboardStatisticData;
use App\Exports\AdminDashboardDataExport\AdminDashboardDataExport;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activites = $this->getActivities()
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
        $paginatedLinks = PaginationService::paginationLinks($activites);

        return Inertia::render('Activity', [
            'activities' => $activites,
            'paginatedLinks' => $paginatedLinks,
        ]);
    }

    /**
     */
    public function getActivities(){
        // Only show investor activities
        $investorIds =  DB::table('model_has_roles')->whereRoleId(2)->pluck('model_id')->all();
        $p = DB::table('file_actions')
            ->whereIn('initiated_by', $investorIds)
            ->select(
                'type as action',
                DB::raw('NULL as folder'),
                DB::raw('NULL as subfolder_id'),
                'initiated_by',
                DB::raw("JSON_OBJECT('file_id', file_id) as action_data"),
                'created_at'
            );

        $activites = DB::table(DB::raw("({$p->toSql()}) AS p"))
            ->mergeBindings($p)
            ->select(
                'action as actionName',
                'folder',
                'subfolder.name as subfolder',
                'initiatorUsers.name as initiatorName',
                'actionDataUsers.name as actionDataUserName',
                'actionDataFiles.name as actionDataFileName',
                'action_data',
                'p.created_at'
            )
            ->join('users as initiatorUsers', 'initiatorUsers.id', '=', "initiated_by")
            ->leftJoin(
                'users as actionDataUsers',
                'actionDataUsers.id',
                DB::Raw("CAST(p.action_data->'$.user_id' AS UNSIGNED)")
            )
            ->leftJoin('file_folders as subfolder', 'subfolder.id', '=', 'subfolder_id')
            ->leftJoin(
                'files as actionDataFiles',
                'actionDataFiles.id',
                DB::Raw("CAST(p.action_data->'$.file_id' AS UNSIGNED)")
            );

        return $activites;
    }

    public function getAnalysisData(Request $request)
    {
        $filterData = $request->all();

        $pagesAnalysisCollection = app(AdminDashboardStatisticData::class)
            ->getAnalysisData($filterData);

        return response()->json($pagesAnalysisCollection, 200);
    }

    public function getUserActivityData(Request $request)
    {
        $filterData = $request->all();
        if ($request->sortBy) {
            $filterData['sortDirection'] = $request->get('sortDirection', 'DESC');
            $filterData['sortBy'] = $request->sortBy;
        }

        $pageNumber = null;
        if ($request->pageNumber && is_numeric($request->pageNumber)) {
            $pageNumber = $request->pageNumber;
        }

        $userActivityData = app(AdminDashboardStatisticData::class)
            ->getUserActivityData($filterData, true, $pageNumber);

        return response()->json($userActivityData, 200);
    }

    public function getFilesStatisticData(Request $request)
    {
        $filterData = $request->all();

        $pageNumber = null;
        if ($request->pageNumber && is_numeric($request->pageNumber)) {
            $pageNumber = $request->pageNumber;
        }

        $fileStaticData = app(AdminDashboardStatisticData::class)
            ->getFilesStatisticData($filterData, true, $pageNumber);

        return response()->json($fileStaticData, 200);
    }

    public function exportToExcel()
    {
        $date = date('Y:m:d h:i:s');

        return Excel::download(new ActivityReportExport($this->getActivities()->get()), "Access Report - {$date}.xlsx");
    }

    public function exportAdminDashboardData(Request $request)
    {
        $filterData = $request->all();
        $date = date('Y:m:d h:i:s');

        return Excel::download(new AdminDashboardDataExport($filterData), "Admin Dashboard Export - {$date}.xlsx");
    }
}
