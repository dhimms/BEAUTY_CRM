<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view('manager.reports.index');
    }
    public function salesPerformance()
    {
        return view('manager.reports.sales-performance');
    }
    public function revenue()
    {
        return view('manager.reports.revenue');
    }
    public function lostReasons()
    {
        return view('manager.reports.lost-reasons');
    }
    public function leadSources()
    {
        return view('manager.reports.lead-sources');
    }
    public function export()
    { /* TODO */
    }
}