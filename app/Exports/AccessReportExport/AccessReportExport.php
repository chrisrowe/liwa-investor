<?php

namespace App\Exports\AccessReportExport;

use App\Exports\AccessReportExport\AccessReportExportSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class AccessReportExport implements WithMultipleSheets
{
    protected $accessReports;

    public function sheets(): array
    {
        $sheets['Access Reports'] = new AccessReportExportSheet($this->accessReports);
        
        return $sheets;
    }

    public function __construct($accessReports)
    {
        $this->accessReports = $accessReports;
    }
}
