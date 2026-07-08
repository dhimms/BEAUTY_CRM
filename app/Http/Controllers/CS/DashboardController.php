<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // TODO: Implement CS dashboard data
        return view('cs.dashboard.index');
    }
}