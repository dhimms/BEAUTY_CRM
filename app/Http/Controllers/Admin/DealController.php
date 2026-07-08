<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DealController extends Controller
{
    public function index()
    {
        return view('admin.deals.index');
    }
    public function show($id)
    {
        return view('admin.deals.show');
    }
    public function edit($id)
    {
        return view('admin.deals.edit');
    }
    public function update()
    { /* TODO */
    }
    public function destroy()
    { /* TODO */
    }
}