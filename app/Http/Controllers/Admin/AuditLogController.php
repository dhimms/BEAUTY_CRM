<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::query()
            ->with('user')
            ->filterAction($request->action)
            ->filterUser($request->user_id)
            ->filterModule($request->module)
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $users = User::orderBy('name')->get();
        $actions = ['created', 'updated', 'deleted'];

        return view('admin.audit-logs.index', compact('logs', 'users', 'actions'));
    }
}
