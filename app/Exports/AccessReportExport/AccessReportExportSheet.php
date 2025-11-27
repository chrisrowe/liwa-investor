<?php

namespace App\Exports\AccessReportExport;

// use App\Models\Distribution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;


class AccessReportExportSheet implements FromCollection, WithMapping, WithEvents
{
    use Exportable, RegistersEventListeners;

    protected $accessReports;
    public static $folderRowsNumber = [];
    public static $rowsNumber = 0;

    public function __construct($accessReports)
    {
        $this->accessReports = $accessReports;
    }

    public function collection()
    {
        return collect($this->accessReports);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => [self::class, 'afterSheet'],
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        foreach (self::$folderRowsNumber as $rowNumber) {
            $event->sheet->getStyle('A' . $rowNumber)->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            $event->sheet->mergeCells("A{$rowNumber}:B{$rowNumber}");
        }
        $event->sheet->getColumnDimension('A')->setAutoSize(true);
        $event->sheet->getColumnDimension('B')->setAutoSize(true);
    }

    public function map($folder): array
    {
        $rowsNumber = self::$rowsNumber;
        $folderRowsNumber = self::$folderRowsNumber;
        $rows = [];
        $rows[] = ['Page "' . ucwords(str_replace('-', ' ', $folder['name'])) . '"'];
        $rowsNumber++;
        $folderRowsNumber[] = $rowsNumber;
        foreach ($folder['investors'] as $investor) {
            $rows[] = [
                $investor['name'],
                $investor['email'],
            ];
            $rowsNumber++;
        }
        foreach ($folder['subfolders'] as $subfolder) {
            $rows[] = ['Folder "' . $subfolder['name'] . '"'];
            $rowsNumber++;
            $folderRowsNumber[] = $rowsNumber;
            foreach ($subfolder['users'] as $investor) {
                $rows[] = [
                    $investor['name'],
                    $investor['email'],
                ];
                $rowsNumber++;
            }
        }
        self::$rowsNumber = $rowsNumber;
        self::$folderRowsNumber = $folderRowsNumber;
        
        return $rows;
    }
}
