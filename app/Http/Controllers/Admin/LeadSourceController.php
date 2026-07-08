<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class LeadSourceController extends Controller
{
    public function index()
    {
        return view('admin.lead-sources.index');
    }
    public function create()
    {
        return view('admin.lead-sources.create');
    }
    public function store()
    { /* TODO */
    }
    public function edit($id)
    {
        return view('admin.lead-sources.edit');
    }
    public function update()
    { /* TODO */
    }
    public function destroy()
    { /* TODO */
    }
}