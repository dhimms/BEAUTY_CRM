<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{   // buat method index untuk menampilkan data audit log
    public function index(Request $request)    
    {     
        $tab = $request->get('tab', 'system');
        $users = User::orderBy('name')->get();

        if ($tab === 'activities') {
            $logs = \App\Models\Activity::query()
                ->with(['user', 'activitable'])
                ->when($request->user_id, fn($q, $v) => $q->where('user_id', $v))
                ->when($request->action, fn($q, $v) => $q->where('type', $v)) // use action for type filter
                ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
                ->when($request->date_to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
                ->latest()
                ->paginate(30)
                ->withQueryString();
            
            $actions = ['call', 'whatsapp', 'email', 'meeting', 'note'];
            
            return view('admin.audit-logs.index', compact('logs', 'users', 'actions', 'tab'));
        }

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
    
        $actions = ['created', 'updated', 'deleted'];

        return view('admin.audit-logs.index', compact('logs', 'users', 'actions', 'tab'));
    }
}
