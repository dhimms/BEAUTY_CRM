<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;

class DealController extends Controller
{
    public function pipeline()
    {
        return view('sales.deals.pipeline');
    }
    public function index()
    {
        return view('sales.deals.index');
    }
    public function create($lead)
    {
        return view('sales.deals.create');
    }
    public function store()
    { /* TODO */
    }
    public function show($id)
    {
        return view('sales.deals.show');
    }
    public function update()
    { /* TODO */
    }
    public function moveStage()
    { /* TODO */
    }
    public function close()
    { /* TODO */
    }
}