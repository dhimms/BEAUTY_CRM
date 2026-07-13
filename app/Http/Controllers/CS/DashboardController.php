<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Services\CustomerService;

class DashboardController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function index()
    {
        $data = $this->customerService->getDashboardData();
        return view('cs.dashboard.index', $data);
    }
}