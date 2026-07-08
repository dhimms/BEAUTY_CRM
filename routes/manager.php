<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\ReportController;
use App\Http\Controllers\Manager\PipelineController;
use App\Http\Controllers\Manager\TeamPerformanceController;

Route::middleware(['role:Manager'])->prefix('manager')->name('manager.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pipeline Overview
    Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
    Route::get('/pipeline/data', [PipelineController::class, 'data'])->name('pipeline.data');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales-performance', [ReportController::class, 'salesPerformance'])->name('reports.sales-performance');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/lost-reasons', [ReportController::class, 'lostReasons'])->name('reports.lost-reasons');
    Route::get('/reports/lead-sources', [ReportController::class, 'leadSources'])->name('reports.lead-sources');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Team Performance
    Route::get('/team', [TeamPerformanceController::class, 'index'])->name('team.index');
    Route::get('/team/{user}', [TeamPerformanceController::class, 'show'])->name('team.show');

    // Forecast
    Route::get('/forecast', [\App\Http\Controllers\Manager\ForecastController::class, 'index'])->name('forecast.index');

    // Audit Log (Manager can view)
    Route::get('/audit-logs', [\App\Http\Controllers\Manager\AuditLogController::class, 'index'])->name('audit-logs.index');
});