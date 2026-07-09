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
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index()
    {
        return view('manager.reports.index');
    }

    public function salesPerformance()
    {
        $salesData = $this->reportService->getSalesPerformance();
        return view('manager.reports.sales-performance', compact('salesData'));
    }

    public function revenue()
    {
        $revenueData = $this->reportService->getRevenueReport(12);
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

    public function export(ExportReportRequest $request)
    {
        $format = $request->input('format', 'xlsx');
        $type = $request->input('report_type');

        return match ($type) {
            'sales-performance' => Excel::download(
                new SalesPerformanceExport($this->reportService),
                "sales-performance.{$format}"
            ),
            'revenue' => Excel::download(
                new RevenueExport($this->reportService),
                "revenue-report.{$format}"
            ),
            default => redirect()->back()->with('error', 'Tipe report tidak valid.'),
        };
    }
}