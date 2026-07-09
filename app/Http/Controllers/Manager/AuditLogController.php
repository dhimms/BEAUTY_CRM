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

    public function index(Request $request)
    {
        $logs = $this->reportService->getAuditLogs(
            $request->only(['action', 'user_id', 'module'])
        );
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('manager.audit-logs.index', compact('logs', 'users'));
    }
}