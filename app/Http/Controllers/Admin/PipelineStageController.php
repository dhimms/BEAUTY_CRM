<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PipelineStageController extends Controller
{
    public function index()
    {
        return view('admin.pipeline-stages.index');
    }
    public function create()
    {
        return view('admin.pipeline-stages.create');
    }
    public function store()
    { /* TODO */
    }
    public function edit($id)
    {
        return view('admin.pipeline-stages.edit');
    }
    public function update()
    { /* TODO */
    }
    public function destroy()
    { /* TODO */
    }
}