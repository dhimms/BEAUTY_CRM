<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// ─── Auth Routes ──────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Protected Routes ─────────────────────────────
Route::middleware(['auth', 'active.user'])->group(function () {

    // Dashboard redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return match (true) {
            $user->isAdmin() => redirect()->route('admin.dashboard'),
            $user->isSales() => redirect()->route('sales.dashboard'),
            $user->isCS() => redirect()->route('cs.dashboard'),
            $user->isManager() => redirect()->route('manager.dashboard'),
            default => redirect()->route('login'),
        };
    })->name('dashboard');

    // Include role-specific route files
    require __DIR__ . '/admin.php';
    require __DIR__ . '/sales.php';
    require __DIR__ . '/cs.php';
    require __DIR__ . '/manager.php';
});