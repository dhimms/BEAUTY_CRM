<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RevenueExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $data;

    public function __construct(ReportService $reportService)
    {
        $this->data = $reportService->getRevenueReport(12);
    }

    public function array(): array
    {
        return array_map(function ($month) {
            return [
                $month['month'],
                $month['revenue'],
                $month['deals_count'],
            ];
        }, $this->data['monthly']);
    }

    public function headings(): array
    {
        return [
            'Bulan',
            'Revenue (Rp)',
            'Deals Won',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }

    public function title(): string
    {
        return 'Revenue Report';
    }
}
