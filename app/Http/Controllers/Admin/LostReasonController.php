<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class LostReasonController extends Controller
{
    public function index()
    {
        return view('admin.lost-reasons.index');
    }
    public function create()
    {
        return view('admin.lost-reasons.create');
    }
    public function store()
    { /* TODO */
    }
    public function edit($id)
    {
        return view('admin.lost-reasons.edit');
    }
    public function update()
    { /* TODO */
    }
    public function destroy()
    { /* TODO */
    }
}