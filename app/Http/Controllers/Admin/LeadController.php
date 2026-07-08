<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeadRequest;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::query()
            ->with(['source', 'assignedUser'])
            ->search($request->search)
            ->filterSource($request->source)
            ->filterStatus($request->status)
            ->filterQualification($request->qualification)
            ->filterAssigned($request->assigned_to)
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $sources   = LeadSource::active()->get();
        $salesList = User::role('Sales')->where('is_active', true)->get();

        return view('admin.leads.index', compact('leads', 'sources', 'salesList'));
    }

    public function create()
    {
        $sources   = LeadSource::active()->get();
        $salesList = User::role('Sales')->where('is_active', true)->get();
        return view('admin.leads.create', compact('sources', 'salesList'));
    }

    public function store(LeadRequest $request)
    {
        $lead = Lead::create($request->validated() + ['created_by' => auth()->id()]);
        return redirect()->route('admin.leads.show', $lead)
            ->with('success', "Lead {$lead->name} berhasil dibuat.");
    }

    public function show(Lead $lead)
    {
        $lead->load(['source', 'assignedUser', 'creator', 'deals.pipelineStage', 'activities.user']);
        return view('admin.leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $sources   = LeadSource::active()->get();
        $salesList = User::role('Sales')->where('is_active', true)->get();
        return view('admin.leads.edit', compact('lead', 'sources', 'salesList'));
    }

    public function update(LeadRequest $request, Lead $lead)
    {
        $lead->update($request->validated());
        return redirect()->route('admin.leads.show', $lead)
            ->with('success', "Lead {$lead->name} berhasil diperbarui.");
    }

    public function destroy(Lead $lead)
    {
        $name = $lead->name;
        $lead->delete();
        return redirect()->route('admin.leads.index')
            ->with('success', "Lead {$name} berhasil dihapus.");
    }
}
