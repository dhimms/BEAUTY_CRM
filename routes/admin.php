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

// ini adalah middleware yang kedua yang berugna untuk mengecek apakah user ini adalah admin atau tidak
// dan juga untuk menentukan prefix dan nama route
// prefix adalah awalan dari url /admin/  
// name adalah awalan dari nama route /admin 
Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    // resource adalah singkatan untuk 7 route sekaligus:
    // index, create, store, show, edit, update, destroy
    Route::resource('users', UserController::class);
    // patch adalah method untuk update data yang pakai togle karena dia hanya sebagian merubah datanya
    Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');

    // Lead Sources
    Route::resource('lead-sources', LeadSourceController::class);
    Route::patch('lead-sources/{leadSource}/toggle', [LeadSourceController::class, 'toggle'])->name('lead-sources.toggle');

    // Pipeline Stages (reorder must be before resource to avoid route conflict)
    Route::patch('pipeline-stages/reorder', [PipelineStageController::class, 'reorder'])->name('pipeline-stages.reorder');
    Route::resource('pipeline-stages', PipelineStageController::class);

    // Lost Reasons
    Route::resource('lost-reasons', LostReasonController::class);

    // Import/Export (must be before resource to avoid route conflict)
    Route::post('/leads/import', [ImportExportController::class, 'import'])->name('leads.import');
    Route::get('/leads/export', [ImportExportController::class, 'export'])->name('leads.export');
    Route::get('/leads/import/template', [ImportExportController::class, 'downloadTemplate'])->name('leads.import.template');

    // Leads (full CRUD for admin)
    Route::resource('leads', LeadController::class);

    // Deals
    Route::resource('deals', DealController::class)->except(['create', 'store']);

    // Customers
    Route::resource('customers', CustomerController::class)->except(['create', 'store']);

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});