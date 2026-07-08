<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class LeadController extends Controller
{
    public function index()
    {
        return view('admin.leads.index');
    }
    public function create()
    {
        return view('admin.leads.create');
    }
    public function store()
    { /* TODO */
    }
    public function show($id)
    {
        return view('admin.leads.show');
    }
    public function edit($id)
    {
        return view('admin.leads.edit');
    }
    public function update()
    { /* TODO */
    }
    public function destroy()
    { /* TODO */
    }
}