<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class ForecastController extends Controller
{
    public function index()
    {
        return view('manager.forecast.index');
    }
}