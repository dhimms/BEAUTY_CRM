<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // TODO: Implement dashboard data
        return view('admin.dashboard');
    }
}