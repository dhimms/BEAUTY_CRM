<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class PipelineController extends Controller
{
    public function index()
    {
        return view('manager.pipeline.index');
    }
    public function data()
    { /* TODO: Return JSON for AJAX */
    }
}