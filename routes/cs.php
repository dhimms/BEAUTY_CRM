<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CS\DashboardController;
use App\Http\Controllers\CS\CustomerController;

use App\Http\Controllers\CS\FollowUpController;

// middleware role:Customer Service digunakan untuk mengecek apakah user memiliki role Customer Service, 
// prefix('cs') digunakan untuk memisahkan route berdasarkan role, name cs. digunakan untuk memisahkan route berdasarkan role 
// jadi misal ada role cs, maka route cs.dashboard akan diarahkan ke controller DashboardController@index dengan prefix cs
Route::middleware(['role:Customer Service'])->prefix('cs')->name('cs.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');


    // Follow-ups
    Route::get('/follow-ups', [FollowUpController::class, 'index'])->name('follow-ups.index');
    Route::post('/follow-ups', [FollowUpController::class, 'store'])->name('follow-ups.store');
    Route::post('/follow-ups/{activity}/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');

    // Activities (for CS context)
    Route::post('/activities', [\App\Http\Controllers\CS\ActivityController::class, 'store'])->name('activities.store');
});