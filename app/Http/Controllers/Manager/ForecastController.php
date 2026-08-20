<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\ReportService;

class ForecastController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index(\Illuminate\Http\Request $request)
    {
        $forecastData = $this->reportService->getForecastData();
        $revenueData = $this->reportService->getRevenueTargetData();
        return view('manager.forecast.index', compact('forecastData', 'revenueData'));
    }

    public function updateTargets(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'global_target' => 'required|numeric|min:0',
        ]);

        $targetAmount = $request->global_target;

        // Update target for all users who have the 'Sales' role
        $salesUsers = \App\Models\User::role('Sales')->get();
        foreach ($salesUsers as $user) {
            $user->update(['revenue_target' => $targetAmount]);
        }

        return redirect()->back()->with('success', 'Target pendapatan bulanan berhasil diperbarui.');
    }
}