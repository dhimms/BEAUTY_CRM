<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Http\Requests\CS\StoreActivityRequest;
use App\Services\CustomerService;

class ActivityController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function store(StoreActivityRequest $request)
    {
        $this->customerService->logActivity($request->validated());

        return redirect()->back()->with('success', 'Aktivitas berhasil dicatat.');
    }
}