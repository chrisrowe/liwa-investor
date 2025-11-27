<?php

namespace App\Exports\AdminDashboardDataExport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

use App\Services\AdminDashboardStatisticData;

class AnalysisDataExportSheet implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    use Exportable, RegistersEventListeners;

    protected $filterData;

    public function __construct($filterData)
    {
        $this->filterData = $filterData;
    }

    public function headings(): array {
        return [
            'Page Name',
            'Users',
            'Page Views',
            'Clicks',
            'Video Views',
        ];
    }

    public function title(): string
    {
        return 'Overview';
    }

    public function styles(Worksheet $sheet)
    {
        return [
           // Style the first row as bold text.
           1    => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getStyle('B2:E2')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getColumnDimension('E')->setAutoSize(true);
            },
        ];
    }

    public function collection()
    {
        return app(AdminDashboardStatisticData::class)->getAnalysisData($this->filterData);
    }
}
