<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    // Tahap 7: Audit Log (CCTV Aplikasi)
    // Fitur ini digunakan Manager untuk melacak "Jejak Digital" (Siapa mengubah apa dan kapan)
    public function index(Request $request)
    {
        // Meminta Service untuk mengambil riwayat log berdasarkan filter (Pelaku, Aksi, dll)
        $logs = $this->reportService->getAuditLogs(
            $request->only(['action', 'user_id', 'module'])
        );
        
        // Mengambil daftar karyawan untuk ditampilkan di form filter web
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('manager.audit-logs.index', compact('logs', 'users'));
    }
}