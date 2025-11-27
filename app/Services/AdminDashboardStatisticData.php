<?php

namespace App\Services;

use App\Models\Page;
use App\Models\FileAction;
use App\Models\FileFolderAction;
use App\Models\User;
use DB;
use Carbon\Carbon;

class AdminDashboardStatisticData
{
	public function getAnalysisData($filterData)
    {
    	$filterDate = isset($filterData['filterDate']) ? $filterData['filterDate'] : null;
        $userPageVisitsQuery = FileFolderAction::
            select(['folder', 'action', 'users.id as userId'])
            ->where('action', 'visit')
            ->groupBy('userId', 'folder', 'action');
        $userPageVisitsQueryString = $this->prepareAnalysisDataQueryString($userPageVisitsQuery, 'file_folder_actions.created_at', $filterDate);

        $fileFolderActionQuery = FileFolderAction::
            select(['folder', 'action', DB::raw('COUNT("id") as count')])
            ->where('action', 'visit')
            ->groupBy('folder', 'action');
        $fileFolderActionQueryString = $this->prepareAnalysisDataQueryString($fileFolderActionQuery, 'file_folder_actions.created_at', $filterDate);

        $fileActionQuery = FileAction::
            select(['f.type', DB::raw('COUNT("f.id") as count')])
            ->join('files as f', 'f.id', '=', 'file_id')
            ->groupBy('f.type');
        $videoActionQuery = clone $fileActionQuery;
        $videoActionQuery = $videoActionQuery->where('f.type', 'videos');

        $fileActionQueryString = $this->prepareAnalysisDataQueryString($fileActionQuery, 'file_actions.created_at', $filterDate);
        $videoActionQueryString = $this->prepareAnalysisDataQueryString($videoActionQuery, 'file_actions.created_at', $filterDate);
        $pagesAnalysisData = Page::
            select(
                [
                    DB::raw('CONCAT(UCASE(LEFT(pages.name, 1)), SUBSTRING(pages.name, 2)) as page_name'),
                    'uffa.count as users',
                    'ffa.count as page_views',
                    'fa.count as clicks',
                    'va.count as video_views'
                ]
            )
            ->leftJoin(
                DB::raw(
                    "(
                        SELECT uffa_temp.folder, COUNT(uffa_temp.userId) as count FROM
                        ( {$userPageVisitsQueryString} ) as uffa_temp
                        GROUP BY uffa_temp.folder
                    ) as uffa"
                ),
                function ($join) {
                    $join->on('uffa.folder', '=', 'pages.name');
                }
            )
            ->leftJoin(
                DB::raw("($fileFolderActionQueryString) as ffa"),
                function ($join) {
                    $join->on('ffa.folder', '=', 'pages.name');
                }
            )
            ->leftJoin(
                DB::raw("($fileActionQueryString) as fa"),
                function ($join) {
                    $join->on('fa.type', '=', 'pages.name');
                }
            )
            ->leftJoin(
                DB::raw("($videoActionQueryString) as va"),
                function ($join) {
                    $join->on('va.type', '=', 'pages.name');
                }
            )
            ->get();

        $pagesAnalysisCollection = collect($pagesAnalysisData);
        foreach ($pagesAnalysisCollection as &$pagesAnalysisCollectionItem) {
        	if ($pagesAnalysisCollectionItem->page_name === 'Other') {
        		$pagesAnalysisCollectionItem->page_name = 'Liwa Fund Reports';
        	}
        }
        $totalData = [
            'page_name' => 'Total',
            'users' => $pagesAnalysisCollection->sum('users'),
            'page_views' => $pagesAnalysisCollection->sum('page_views'),
            'clicks' => $pagesAnalysisCollection->sum('clicks'),
            'video_views' => $pagesAnalysisCollection->sum('video_views'),
        ];
        $pagesAnalysisCollection->prepend($totalData);

        return $pagesAnalysisCollection;
    }

    public function getUserActivityData($filterData, $paginate = true, $pageNumber = null)
    {
        $query = FileAction::
            select([
                'users.name as user_name',
                'file_actions.created_at as date_accessed',
                'f.type as folder',
                'f.name as file_video_viewed',
            ])
            ->join('files as f', 'f.id', '=', 'file_id');

        if (isset($filterData['sortBy'])) {
            $sortDirection = isset($filterData['sortDirection']) ? $filterData['sortDirection'] : 'DESC';
            $query = $query->orderBy($filterData['sortBy'], $sortDirection);
        }

        $filterDate = isset($filterData['filterDate']) ? $filterData['filterDate'] : null;

        $query = $this->prepareAnalysisDataSubQuery($query, 'file_actions.created_at', $filterDate);
        $userActivityData = [];
        if ($paginate) {
	        $userActivityData = $query->paginate(25, ['*'], 'page', $pageNumber);
        } else {
        	$userActivityData = $query->get();
        }

        foreach ($userActivityData as &$userActivityDataItem) {
        	if ($userActivityDataItem['folder'] === 'other') {
        		$userActivityDataItem['folder'] = 'liwa fund reports';
        	}
        }

        return $userActivityData;
    }

    public function getFilesStatisticData($filterData, $paginate = true, $pageNumber = null)
    {
    	$filterDate = isset($filterData['filterDate']) ? $filterData['filterDate'] : null;

        $query = FileAction::
            select([
                'f.type as folder',
                'f.name as file_name',
                DB::raw('COUNT(file_actions.id) as clicks'),
                DB::raw('IF(f.type = "videos", COUNT(file_actions.id), 0) as video_views'),
            ])
            ->join('files as f', 'f.id', '=', 'file_id')
            ->groupBy('f.id');

        $query = $this->prepareAnalysisDataSubQuery($query, 'file_actions.created_at', $filterDate);

        $fileStaticsData = [];
        if ($paginate) {
	        $fileStaticsData = $query->paginate(10, ['*'], 'page', $pageNumber);
        } else {
        	$fileStaticsData = $query->get();
        }

        foreach ($fileStaticsData as &$fileStaticsDataItem) {
        	if ($fileStaticsDataItem['folder'] === 'other') {
        		$fileStaticsDataItem['folder'] = 'liwa fund reports';
        	}
        }

        return $fileStaticsData;
    }

    protected function prepareAnalysisDataSubQuery($query, $filterDateColumn, $filterPeriod = null)
    {
        $query = $query
            ->join('users', 'users.id', '=', 'initiated_by')
            ->join('model_has_roles', function($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where(function ($q) {
                        $q->where('model_has_roles.model_type', '=', User::class)
                            ->orWhere('model_has_roles.model_type', '=', addslashes(User::class));
                    });
            })
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', '=', 'investor');

        if ($filterPeriod) {
            $filterDate = null;
            switch ($filterPeriod) {
                case 'past_day':
                    $filterDate = Carbon::now()->subDays(1);
                    break;
                case 'last_3_days':
                    $filterDate = Carbon::now()->subDays(3);
                    break;
                case 'last_week':
                    $filterDate = Carbon::now()->subDays(7);
                    break;
                case 'last_month':
                    $filterDate = Carbon::now()->subMonths(1);
                    break;
                case '3_month':
                    $filterDate = Carbon::now()->subMonths(3);
                    break;
                case 'half_year':
                    $filterDate = Carbon::now()->subMonths(6);
                    break;
                case 'year':
                    $filterDate = Carbon::now()->subMonths(12);
                    break;
                case 'all_time':
                default:
                    break;
            }

            if ($filterDate) {
                $query = $query->where($filterDateColumn, '>=', $filterDate);
            }
        }
        
        return $query;
    }

    protected function prepareAnalysisDataQueryString($query, $filterDateColumn, $filterPeriod = null)
    {
        $query = $this->prepareAnalysisDataSubQuery($query, $filterDateColumn, $filterPeriod);

        $queryString = str_replace(array('?'), array('\'%s\''), $query->toSql());
        $queryString = vsprintf($queryString, $query->getBindings());

        return $queryString;
    }
}