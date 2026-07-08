<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class TeamPerformanceController extends Controller
{
    public function index()
    {
        return view('manager.reports.index');
    }
    public function show($id)
    { /* TODO */
    }
}