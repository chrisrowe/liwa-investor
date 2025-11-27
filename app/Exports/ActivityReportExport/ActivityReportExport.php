<?php

namespace App\Exports\ActivityReportExport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ActivityReportExport implements FromCollection, WithHeadings
{
    protected $activities;


    public function __construct($activities)
    {
        $this->activities = $activities;
    }

    public function headings(): array {
        return [
            'User Name',
            'Date Accessed',
            'Folder',
            'File/Video Viewed'
        ];
    }

    public function collection()
    {
        $activitiesCollection = collect();
        foreach($this->activities as $activity){
            $activitiesCollection->push([
                $activity->initiatorName,
                $activity->created_at,
                $activity->folder,
                $activity->actionDataFileName
            ]);
        }
        return $activitiesCollection;
    }

}
