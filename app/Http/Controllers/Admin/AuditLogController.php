<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AuditLogController extends Controller
{
    public function index()
    {
        return view('admin.audit-logs.index');
    }
}