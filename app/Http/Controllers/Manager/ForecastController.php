<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\ReportService;

class ForecastController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index()
    {
        $forecastData = $this->reportService->getForecastData();
        return view('manager.forecast.index', compact('forecastData'));
    }
}