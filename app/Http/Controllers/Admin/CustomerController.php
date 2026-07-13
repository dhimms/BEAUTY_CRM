<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->with(['lead', 'csUser'])
            ->search($request->search)
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->cs_id, fn($q, $v) => $q->where('user_id', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $csList = \App\Models\User::role('Customer Service')->where('is_active', true)->orderBy('name')->get();

        return view('admin.customers.index', compact('customers', 'csList'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['lead', 'csUser', 'serviceTickets', 'activities.user']);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $customer->load(['lead', 'csUser']);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,churn'],
            'notes' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'],
        ]);

        $validated['tags'] = $request->tags
            ? array_map('trim', explode(',', $request->tags))
            : [];

        $customer->update($validated);
        return redirect()->route('admin.customers.show', $customer)
            ->with('success', "Customer {$customer->name} berhasil diperbarui.");
    }

    public function destroy(Customer $customer)
    {
        $name = $customer->name;
        $customer->delete();
        return redirect()->route('admin.customers.index')
            ->with('success', "Customer {$name} berhasil dihapus.");
    }
}
