<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LostReasonRequest;
use App\Models\LostReason;

class LostReasonController extends Controller
{
    // method index untuk menampilkan data lost reasons
    // di sini saya menambahkan withCount('deals') untuk menghitung jumlah deal yang memiliki lost reason ini
    // latest() untuk mengurutkan data berdasarkan created_at terbaru 
    // paginate(20) untuk membatasi jumlah data yang ditampilkan per halaman  
    public function index()                     
    {
        $reasons = LostReason::withCount('deals')->latest()->paginate(20);
        return view('admin.lost-reasons.index', compact('reasons'));
    }
 
    public function create()
    {
        return view('admin.lost-reasons.create');
    }

    // Method store untuk menyimpan data Lost Reason baru ke database.
    // Kita menggunakan FormRequest (LostReasonRequest) sebagai "Satpam" di pintu masuk.
    // Jika lolos validasi, method $request->validated() akan mengambil data yang sudah bersih,
    // lalu diserahkan ke Model untuk disimpan ke database.
    public function store(LostReasonRequest $request)
    {
        LostReason::create($request->validated());
        return redirect()->route('admin.lost-reasons.index')
            ->with('success', 'Lost reason berhasil ditambahkan.');
    }

    public function edit(LostReason $lostReason)
    {
        return view('admin.lost-reasons.edit', compact('lostReason'));
    }

    public function update(LostReasonRequest $request, LostReason $lostReason)
    {
        $lostReason->update($request->validated());
        return redirect()->route('admin.lost-reasons.index')
            ->with('success', 'Lost reason berhasil diperbarui.');
    }

    // Method destroy digunakan untuk menghapus data Lost Reason.
    // Namun, sebelum menghapus, kita melakukan pengecekan dulu:
    // $lostReason->deals()->exists() -> Apakah ada data Deal yang masih "numpang" di Lost Reason ini?
    // Jika ada (returns true), maka akan muncul pesan error dan proses hapus dibatalkan (back()).
    // Jika tidak ada (aman untuk dihapus), maka data akan dihapus (delete()).
    public function destroy(LostReason $lostReason)
    {
        if ($lostReason->deals()->exists()) {
            return back()->with('error', 'Lost reason tidak bisa dihapus karena masih digunakan pada deal.');
        }
        $lostReason->delete();
        return redirect()->route('admin.lost-reasons.index')
            ->with('success', 'Lost reason berhasil dihapus.');
    }
}
