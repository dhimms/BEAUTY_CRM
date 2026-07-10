<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Hanya menampilkan customer dari deal yang sales ini menangkan
        $customers = Customer::whereHas('lead', function ($query) {
                $query->where('assigned_to', auth()->id())
                      ->whereHas('deals', function ($q) {
                          $q->where('status', 'won')
                            ->where('assigned_to', auth()->id());
                      });
            })
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('sales.customers.index', compact('customers'));
    }
}
