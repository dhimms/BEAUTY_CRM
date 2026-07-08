<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class AuditLogController extends Controller
{
    public function index()
    {
        return view('manager.audit-logs.index');
    }
}