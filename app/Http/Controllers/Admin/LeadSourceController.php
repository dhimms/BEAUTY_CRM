<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeadSourceRequest;
use App\Models\LeadSource;

class LeadSourceController extends Controller
{
    public function index()
    {
        $sources = LeadSource::withCount('leads')->orderBy('name')->get();
        return view('admin.lead-sources.index', compact('sources'));
    }

    public function create()
    {
        return view('admin.lead-sources.create');
    }

    public function store(LeadSourceRequest $request)
    {
        LeadSource::create($request->validated() + ['is_active' => $request->boolean('is_active', true)]);
        return redirect()->route('admin.lead-sources.index')
            ->with('success', 'Lead source berhasil ditambahkan.');
    }

    public function edit(LeadSource $leadSource)
    {
        return view('admin.lead-sources.edit', compact('leadSource'));
    }

    public function update(LeadSourceRequest $request, LeadSource $leadSource)
    {
        $leadSource->update($request->validated() + ['is_active' => $request->boolean('is_active', true)]);
        return redirect()->route('admin.lead-sources.index')
            ->with('success', 'Lead source berhasil diperbarui.');
    }

    public function destroy(LeadSource $leadSource)
    {
        if ($leadSource->leads()->exists()) {
            return back()->with('error', 'Lead source tidak bisa dihapus karena memiliki leads terkait.');
        }
        $leadSource->delete();
        return redirect()->route('admin.lead-sources.index')
            ->with('success', 'Lead source berhasil dihapus.');
    }

    public function toggle(LeadSource $leadSource)
    {
        $leadSource->update(['is_active' => !$leadSource->is_active]);
        return response()->json([
            'success'   => true,
            'is_active' => $leadSource->is_active,
        ]);
    }
}
