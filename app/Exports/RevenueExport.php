<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// Tahap 7: Export Laporan Excel
// Class ini menandatangani "Kontrak Kerja" (Interfaces) dari library Maatwebsite\Excel
// Kontrak ini mewajibkan class memiliki fungsi-fungsi khusus: array(), headings(), styles(), title()
class RevenueExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $data;

    public function __construct(ReportService $reportService)
    {
        $this->data = $reportService->getMemberAcquisitionReport(12);
    }

    // 1. KONTRAK ISI DATA (FromArray)
    // Fungsi ini wajib mengembalikan susunan data per baris di Excel
    public function array(): array
    {
        return array_map(function ($month) {
            return [
                $month['month'],
                $month['deals_count'],
            ];
        }, $this->data['monthly']);
    }

    // 2. KONTRAK JUDUL KOLOM / HEADER (WithHeadings)
    // Baris pertama di Excel (A1, B1) akan diisi oleh tulisan ini
    public function headings(): array
    {
        return [
            'Bulan',
            'Member Baru',
        ];
    }

    // 3. KONTRAK MENGHIAS EXCEL (WithStyles)
    // Digunakan untuk menebalkan (Bold) baris ke-1 (Header)
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }

    public function title(): string
    {
        return 'Member Acquisition Report';
    }
}
