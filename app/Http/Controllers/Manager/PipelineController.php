<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\PipelineStage;
use App\Services\ReportService;

class PipelineController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index()
    {
        $stages = PipelineStage::ordered()->get();
        return view('manager.pipeline.index', compact('stages'));
    }

    public function data()
    {
        return response()->json([
            'success' => true,
            'pipeline' => $this->reportService->getPipelineData(),
        ]);
    }
}