<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LeadSourceController;
use App\Http\Controllers\Admin\PipelineStageController;
use App\Http\Controllers\Admin\LostReasonController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\DealController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ImportExportController;

Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('users', UserController::class);

    // Lead Sources
    Route::resource('lead-sources', LeadSourceController::class);

    // Pipeline Stages
    Route::resource('pipeline-stages', PipelineStageController::class);

    // Lost Reasons
    Route::resource('lost-reasons', LostReasonController::class);

    // Leads (full CRUD for admin)
    Route::resource('leads', LeadController::class);

    // Deals
    Route::resource('deals', DealController::class)->except(['create', 'store']);

    // Customers
    Route::resource('customers', CustomerController::class)->except(['create', 'store']);

    // Import/Export
    Route::post('/leads/import', [ImportExportController::class, 'import'])->name('leads.import');
    Route::get('/leads/export', [ImportExportController::class, 'export'])->name('leads.export');
    Route::get('/leads/import/template', [ImportExportController::class, 'downloadTemplate'])->name('leads.import.template');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});