<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Muncul di: Menu Sidebar -> "Customers" (/sales/customers)
     * Tampilan: resources/views/sales/customers/index.blade.php
     * Penjelasan: Menampilkan daftar pelanggan (Customers) yang berhasil didapatkan dari Deal berstatus WON milik sales yang sedang login.
     */
    public function index(Request $request)
    {
        // Menentukan data mana saja yang boleh dilihat oleh Sales yang sedang login (hanya dari deal WON milik sendiri)
        $customers = Customer::whereHas('lead', function ($query) {
                $query->where('assigned_to', auth()->id())
                      ->whereHas('deals', function ($q) {
                          $q->where('status', 'won')
                            ->where('assigned_to', auth()->id());
                      });
            })
            // Filter pencarian nama/email/telepon
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
