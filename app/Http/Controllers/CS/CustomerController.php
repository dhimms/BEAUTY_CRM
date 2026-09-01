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
    // digunakan  untuk melihat biodata customer
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function index(Request $request)
    {
        $customers = $this->customerService->getCustomers($request->only(['search', 'status', 'user_id', 'min_spend', 'deal_keyword']));
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

    // controller mengarahkan customerservice untuk menampilkan data customer sesuai ID nya
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

    public function blast(Request $request)
    {
        $request->validate([
            'customer_ids'   => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
            'channel'        => 'required|in:whatsapp,email',
            'message'        => 'required|string',
            'image'          => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $count = $this->customerService->blastMessage(
            $request->customer_ids,
            $request->channel,
            $request->message,
            $request->file('image')
        );

        return back()->with('success', "Pesan blast berhasil dikirim ke $count customer via " . ucfirst($request->channel));
    }
}