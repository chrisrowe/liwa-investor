<?php

namespace App\Exports\AdminDashboardDataExport;

use App\Exports\AdminDashboardDataExport\AnalysisDataExportSheet;
use App\Exports\AdminDashboardDataExport\FileStatisticDataExportSheet;
use App\Exports\AdminDashboardDataExport\UserActivityDataExportSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class AdminDashboardDataExport implements WithMultipleSheets
{
    protected $filterData;

    public function sheets(): array
    {
        $sheets['Overview'] = new AnalysisDataExportSheet($this->filterData);
        $sheets['Page Analytics'] = new FileStatisticDataExportSheet($this->filterData);
        $sheets['User Activity'] = new UserActivityDataExportSheet($this->filterData);
        
        return $sheets;
    }

    public function __construct($filterData)
    {
        $this->filterData = $filterData;
    }
}
