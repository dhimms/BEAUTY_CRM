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
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $data = $this->reportService->getManagerDashboard($period, $startDate, $endDate);
        $data['period'] = $period;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        return view('manager.dashboard.index', $data);
    }
}