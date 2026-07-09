<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Http\Requests\CS\StoreFollowUpRequest;
use App\Models\Activity;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function index(Request $request)
    {
        $data = $this->customerService->getFollowUps($request->only(['from', 'to']));
        $customers = Customer::orderBy('name')->get();

        return view('cs.follow-ups.index', array_merge($data, compact('customers')));
    }

    public function store(StoreFollowUpRequest $request)
    {
        $this->customerService->createFollowUp($request->validated());

        return redirect()->route('cs.follow-ups.index')
            ->with('success', 'Follow-up berhasil dijadwalkan.');
    }

    public function complete(Activity $activity)
    {
        $this->customerService->completeFollowUp($activity);

        return redirect()->route('cs.follow-ups.index')
            ->with('success', 'Follow-up berhasil ditandai selesai.');
    }
}