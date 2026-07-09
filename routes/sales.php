<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sales\DashboardController;
use App\Http\Controllers\Sales\LeadController;
use App\Http\Controllers\Sales\DealController;
use App\Http\Controllers\Sales\ActivityController;
use App\Http\Controllers\Sales\CustomerController;

Route::middleware(['role:Sales'])->prefix('sales')->name('sales.')->group(function () {
    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');


    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Leads (CRUD + qualify + convert)
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::post('/leads/{lead}/qualify', [LeadController::class, 'qualify'])->name('leads.qualify');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

    // Pipeline / Deals
    Route::get('/pipeline', [DealController::class, 'pipeline'])->name('deals.pipeline');
    Route::get('/deals', [DealController::class, 'index'])->name('deals.index');
    Route::get('/deals/create/{lead}', [DealController::class, 'create'])->name('deals.create');
    Route::post('/deals', [DealController::class, 'store'])->name('deals.store');
    Route::get('/deals/{deal}', [DealController::class, 'show'])->name('deals.show');
    Route::put('/deals/{deal}', [DealController::class, 'update'])->name('deals.update');
    Route::post('/deals/{deal}/move-stage', [DealController::class, 'moveStage'])->name('deals.move-stage');
    Route::post('/deals/{deal}/close', [DealController::class, 'close'])->name('deals.close');

    // Activities
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    Route::post('/activities/{activity}/complete-followup', [ActivityController::class, 'completeFollowUp'])->name('activities.complete-followup');
});