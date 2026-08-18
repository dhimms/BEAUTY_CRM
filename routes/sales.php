<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sales\DashboardController;
use App\Http\Controllers\Sales\LeadController;
use App\Http\Controllers\Sales\DealController;
use App\Http\Controllers\Sales\ActivityController;
use App\Http\Controllers\Sales\CustomerController;

Route::middleware(['role:Sales'])->prefix('sales')->name('sales.')->group(function () {
    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index'); // -> CustomerController@index | Menu Sidebar "Customers" | Daftar pelanggan Deal Won

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // -> DashboardController@index | Menu Sidebar "Dashboard" | Grafik & statistik KPI sales

    // Leads (CRUD + qualify + convert)
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index'); // -> LeadController@index | Menu Sidebar "Leads" | Daftar calon pelanggan (leads)
    Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create'); // -> LeadController@create | Tombol "+ Tambah Lead" | Form tambah lead baru
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store'); // -> LeadController@store | Submit Form Tambah Lead | Simpan data lead baru ke DB
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show'); // -> LeadController@show | Klik nama Lead / "Lihat Detail" | Halaman detail lead
    Route::post('/leads/{lead}/qualify', [LeadController::class, 'qualify'])->name('leads.qualify'); // -> LeadController@qualify | Modal/Tombol "Qualify" | Kualifikasi (Qualified/Unqualified/Not Fit)
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert'); // -> LeadController@convert | Tombol "Convert to Deal" | Form buat deal dari lead qualified

    // Pipeline / Deals
    Route::get('/pipeline', [DealController::class, 'pipeline'])->name('deals.pipeline'); // -> DealController@pipeline | Menu Sidebar "Pipeline" | Board Kanban deal per-stage
    Route::get('/deals', [DealController::class, 'index'])->name('deals.index'); // -> DealController@index | Menu "Deals" / "List View" | Tabel daftar deal
    Route::get('/deals/create/{lead}', [DealController::class, 'create'])->name('deals.create'); // -> DealController@create | Tombol "Convert to Deal" | Form buat deal baru
    Route::post('/deals', [DealController::class, 'store'])->name('deals.store'); // -> DealController@store | Submit Form Buat Deal | Simpan deal & ubah status lead -> 'converted'
    Route::get('/deals/{deal}', [DealController::class, 'show'])->name('deals.show'); // -> DealController@show | Klik Card Kanban / Row Tabel | Halaman detail deal
    Route::put('/deals/{deal}', [DealController::class, 'update'])->name('deals.update'); // -> DealController@update | Modal/Form "Edit Deal" | Edit nilai, nama, tgl close deal
    Route::post('/deals/{deal}/move-stage', [DealController::class, 'moveStage'])->name('deals.move-stage'); // -> DealController@moveStage | Drag & Drop Kanban / "Pindah Stage" | Memindahkan stage deal (AJAX)
    Route::post('/deals/{deal}/close', [DealController::class, 'close'])->name('deals.close'); // -> DealController@close | Tombol "Mark as Won/Lost" | Tutup deal (Won -> otomatis buat Customer)

    // Activities
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store'); // -> ActivityController@store | Form "Tambah Aktivitas" | Simpan log aktivitas & follow-up
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update'); // -> ActivityController@update | Tombol "Edit Aktivitas" | Edit catatan / jadwal follow-up(belum di buat)
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy'); // -> ActivityController@destroy | Tombol Hapus (Icon Sampah) | Hapus log aktivitas
    Route::post('/activities/{activity}/complete-followup', [ActivityController::class, 'completeFollowUp'])->name('activities.complete-followup'); // -> ActivityController@completeFollowUp | Checkbox "Selesai" | Ubah status follow-up -> 'done'
});