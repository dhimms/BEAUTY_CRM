<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImportExportService;
use Illuminate\Http\Request;

class ImportExportController extends Controller
{
    public function __construct(private ImportExportService $service) {}

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $result = $this->service->importLeads($request->file('file'));

        if ($result['failure_count'] > 0) {
            $messages = collect($result['failures'])->map(
                fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors())
            )->implode(' | ');

            return redirect()->route('admin.leads.index')
                ->with('error', "Import selesai dengan {$result['failure_count']} error: {$messages}");
        }

        return redirect()->route('admin.leads.index')
            ->with('success', 'Import leads berhasil.');
    }

    public function export(Request $request)
    {
        return $this->service->exportLeads($request->only([
            'search', 'status', 'source', 'qualification', 'assigned_to', 'date_from', 'date_to', 'period',
        ]));
    }

    public function downloadTemplate()
    {
        return $this->service->downloadLeadTemplate();
    }
}
