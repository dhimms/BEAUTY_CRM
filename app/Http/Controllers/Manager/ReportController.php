<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\ExportReportRequest;
use App\Exports\SalesPerformanceExport;
use App\Exports\RevenueExport;
use App\Services\ReportService;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // Ini disebut Dependency Injection. 
    // Kita menyuntikkan Service (Koki) ke dalam Controller (Resepsionis).
    // Tujuannya agar Controller tidak memuat perhitungan matematika/database yang berat.
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index()
    {
        return view('manager.reports.index');
    }
    
    public function salesPerformance()
    {
        // controller meminta data sales performance ke service
        $salesData = $this->reportService->getSalesPerformance();
        // controller lalu me-return data tersebut ke view
        return view('manager.reports.sales-performance', compact('salesData'));
    }

    public function revenue()
    {
        // Meskipun nama fungsinya "revenue", kita sudah mengubah targetnya menjadi Member Baru.
        // Di sini Controller cukup melempar tugas ke Service untuk menghitung tren selama 12 bulan terakhir.
        $revenueData = $this->reportService->getMemberAcquisitionReport(12);
        return view('manager.reports.revenue', compact('revenueData'));
    }

    public function lostReasons()
    {
        $lostData = $this->reportService->getLostReasons();
        return view('manager.reports.lost-reasons', compact('lostData'));
    }

    public function leadSources()
    {
        $sourcesData = $this->reportService->getLeadSources();
        return view('manager.reports.lead-sources', compact('sourcesData'));
    }

    public function pipelineAnalysis()
    {
        $pipelineData = $this->reportService->getPipelineData();
        return view('manager.reports.pipeline-analysis', compact('pipelineData'));
    }

    public function teamActivity()
    {
        $activities = \App\Models\Activity::whereHas('user', function($q) {
            $q->role('Sales');
        })->with(['user', 'activitable'])->latest()->paginate(20);
        return view('manager.reports.team-activity', compact('activities'));
    }

    // Tahap 7: Fitur Export Laporan Excel
    public function export(ExportReportRequest $request)
    {
        $format = $request->input('format', 'xlsx');
        $type = $request->input('report_type');

        // Fitur match (seperti switch-case modern) untuk menentukan laporan apa yang di-download
        return match ($type) {
            'sales-performance' => Excel::download(
                new SalesPerformanceExport($this->reportService),
                "sales-performance.{$format}"
            ),
            'revenue' => Excel::download(
                new RevenueExport($this->reportService), // Memanggil class Export yang bertugas merakit Excel
                "revenue-report.{$format}"
            ),
            default => redirect()->back()->with('error', 'Tipe report tidak valid.'),
        };
    }
}