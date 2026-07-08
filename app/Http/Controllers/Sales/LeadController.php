<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;

class LeadController extends Controller
{
    public function index()
    {
        return view('sales.leads.index');
    }
    public function show($id)
    {
        return view('sales.leads.show');
    }
    public function qualify($id)
    { /* TODO */
    }
    public function convert($id)
    { /* TODO */
    }
}