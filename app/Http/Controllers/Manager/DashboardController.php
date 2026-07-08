<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // TODO: Implement manager dashboard data
        return view('manager.dashboard.index');
    }
}