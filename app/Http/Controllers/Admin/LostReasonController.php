<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LostReasonRequest;
use App\Models\LostReason;

class LostReasonController extends Controller
{
    public function index()
    {
        $reasons = LostReason::withCount('deals')->latest()->paginate(20);
        return view('admin.lost-reasons.index', compact('reasons'));
    }

    public function create()
    {
        return view('admin.lost-reasons.create');
    }

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
