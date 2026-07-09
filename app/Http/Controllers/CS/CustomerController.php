<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Http\Requests\CS\StoreCustomerRequest;
use App\Http\Requests\CS\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function index(Request $request)
    {
        $customers = $this->customerService->getCustomers($request->only(['search', 'status', 'user_id']));
        $csUsers = $this->customerService->getCsUsers();

        return view('cs.customers.index', compact('customers', 'csUsers'));
    }

    public function create()
    {
        $csUsers = $this->customerService->getCsUsers();
        return view('cs.customers.create', compact('csUsers'));
    }

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        $this->customerService->createCustomer($data);

        return redirect()->route('cs.customers.index')
            ->with('success', 'Customer berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        $customer = $this->customerService->getCustomerDetail($customer->id);
        $csUsers = $this->customerService->getCsUsers();

        return view('cs.customers.show', compact('customer', 'csUsers'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $this->customerService->updateCustomer($customer, $request->validated());

        return redirect()->route('cs.customers.show', $customer)
            ->with('success', 'Data customer berhasil diperbarui.');
    }
}