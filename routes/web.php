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

    // Profile routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    Route::post('/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');

    // Dashboard redirect based on role
    // setiap user yg akan login akan masuk ke sini dan di redirect sesuai role nya
    // misal role cs akan masuk ke dashboard cs, role manager akan masuk ke dashboard manager
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
    // digunakan untuk memisahkan route berdasarkan role misal admin.php berisi route untuk admin, cs.php berisi route untuk cs, dst
    require __DIR__ . '/admin.php';
    require __DIR__ . '/sales.php';
    require __DIR__ . '/cs.php';
    require __DIR__ . '/manager.php';
});