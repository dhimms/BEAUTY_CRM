<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function index()
    {
        return view('cs.customers.index');
    }
    public function create()
    {
        return view('cs.customers.create');
    }
    public function store()
    { /* TODO */
    }
    public function show($id)
    {
        return view('cs.customers.show');
    }
    public function update()
    { /* TODO */
    }
}