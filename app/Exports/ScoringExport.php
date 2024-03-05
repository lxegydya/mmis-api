<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScoringExport implements FromCollection, WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $exportData = [];
        $menteeData = [];

        foreach($this->data as $mentee) {
            $menteeName = $mentee['name'];

            foreach ($mentee['scoring_list'] as $scoring) {
                $assignmentName = $scoring['name'];
                $score = $scoring['score'];

                if (!isset($menteeData[$menteeName])) {
                    $menteeData[$menteeName] = ['Nama' => $menteeName];
                }

                $menteeData[$menteeName][$assignmentName] = $score;
            }
        }

        foreach ($menteeData as $data) {
            $exportData[] = $data;
        }

        return collect($exportData);
    }

    public function headings(): array
    {
        $allAssignment = collect($this->data)->flatMap(function ($mentee) {
            return collect($mentee['scoring_list'])->pluck('name');
        })->unique()->values()->toArray();

        $headings = array_merge(['Mentee Name'], $allAssignment);

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $headerRow = 'A1:' . $highestColumn . '1';
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                ],
            ],
        ];

        // Pengaturan Header
        $sheet->getStyle($headerRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle($headerRow)->getFill()->getStartColor()->setARGB('FAA0A0');
        $sheet->getStyle($headerRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRow)->getFont()->setBold(true);

        // Pengaturan Baris
        $sheet->getColumnDimension('A')->setAutoSize(true);
        for ($i = 2; $i <= Coordinate::columnIndexFromString($sheet->getHighestDataColumn()); $i++) {
            $column = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($column)->setWidth(15);
        }

        $sheet->getStyle('B1:' . $sheet->getHighestDataColumn() . '1')->getAlignment()->setWrapText(true);
        $highestRow = $sheet->getHighestDataRow();
        $range = 'B1:' . $sheet->getHighestDataColumn() . $highestRow;

        $sheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        for($i=2; $i <= $highestRow; $i++){
            $sheet->getRowDimension($i)->setRowHeight(25);
            $sheet->getStyle($i . ':' . $i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        // Pengaturan Border
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray($styleArray);

        // Pengaturan Freeze
        $sheet->freezePane('B2');
    }
}
