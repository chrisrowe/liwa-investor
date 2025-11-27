<?php

namespace App\Exports\AdminDashboardDataExport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

use App\Services\AdminDashboardStatisticData;

class UserActivityDataExportSheet implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    use Exportable, RegistersEventListeners;

    protected $filterData;

    public function __construct($filterData)
    {
        $this->filterData = $filterData;
    }

    public function headings(): array {
        return [
            'User Name',
            'Date Accessed',
            'Folder',
            'File/Video Viewed',
        ];
    }

    public function title(): string
    {
        return 'User Activity';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:N1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->setAutoFilter('A1:D1');
            },
        ];
    }

    public function collection()
    {
        $userActivityData = app(AdminDashboardStatisticData::class)
            ->getUserActivityData($this->filterData, false);

        return collect($userActivityData);
    }
}
