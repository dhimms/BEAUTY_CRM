<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesPerformanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $data;

    public function __construct(ReportService $reportService)
    {
        $this->data = $reportService->getSalesPerformance();
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Sales Person',
            'Leads',
            'Qualified',
            'Deals',
            'Won',
            'Lost',
            'Win Rate (%)',
            'Revenue (Rp)',
            'Avg Deal Value (Rp)',
            'Activities',
        ];
    }

    public function map($row): array
    {
        return [
            $row['name'],
            $row['leads'],
            $row['qualified'],
            $row['deals'],
            $row['won'],
            $row['lost'],
            $row['win_rate'],
            $row['revenue'],
            $row['avg_deal_value'],
            $row['activities'],
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
        return 'Sales Performance';
    }
}
