<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\ReportService;

class DashboardController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index(\Illuminate\Http\Request $request)
    {
        // 1. Menangkap filter tanggal dari URL (misal user memilih "this_month")
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // 2. memanggil service untuk mengambil data dashboard berdasarkan filter tanggal
        $data = $this->reportService->getManagerDashboard($period, $startDate, $endDate);
        $data['period'] = $period;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        // 3. mengirim data  ke view
        return view('manager.dashboard.index', $data);
    }
}