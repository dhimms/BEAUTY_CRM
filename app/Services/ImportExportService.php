<?php

namespace App\Services;

use App\Exports\LeadExport;
use App\Imports\LeadImport;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportExportService
{
    /**
     * Import leads from uploaded Excel/CSV file.
     */
    public function importLeads(UploadedFile $file): array
    {
        $import = new LeadImport();
        Excel::import($import, $file);

        $failures = $import->failures();
        return [
            'failures' => $failures,
            'failure_count' => count($failures),
        ];
    }

    /**
     * Export leads with optional filters.
     */
    public function exportLeads(array $filters = []): BinaryFileResponse
    {
        $filename = 'leads_export_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new LeadExport($filters), $filename);
    }

    /**
     * Generate a downloadable import template.
     */
    public function downloadLeadTemplate(): BinaryFileResponse
    {
        $filename = 'lead_import_template.xlsx';

        // Build a simple template with headers only
        $template = new class implements \Maatwebsite\Excel\Concerns\FromArray,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\ShouldAutoSize {
            public function array(): array
            {
                return [
                    ['Contoh Nama', 'contoh@email.com', '081234567890', 'Jl. Contoh No. 1', 'Instagram', 'Catatan lead'],
                ];
            }
            public function headings(): array
            {
                return ['name', 'email', 'phone', 'address', 'lead_source', 'notes'];
            }
        };

        return Excel::download($template, $filename);
    }
}
