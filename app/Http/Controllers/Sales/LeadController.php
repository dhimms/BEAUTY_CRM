<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\QualifyLeadRequest;
use App\Http\Requests\Sales\StoreLeadRequest;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Services\DealService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::where('assigned_to', auth()->id())
            ->filterStatus($request->status)
            ->filterSource($request->source)
            ->filterQualification($request->qualification)
            ->search($request->search)
            ->with(['source', 'assignedUser'])
            ->withCount('deals')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $sources = LeadSource::where('is_active', true)->get();

        return view('sales.leads.index', compact('leads', 'sources'));
    }

    public function create()
    {
        $sources = LeadSource::where('is_active', true)->get();
        return view('sales.leads.create', compact('sources'));
    }

    public function store(StoreLeadRequest $request)
    {
        $data = $request->validated();
        $data['assigned_to'] = auth()->id();
        $data['created_by']  = auth()->id();
        $data['status']      = 'new';

        $lead = Lead::create($data);

        return redirect()->route('sales.leads.show', $lead)
            ->with('success', 'Lead baru berhasil ditambahkan.');
    }

    public function show(Lead $lead)
    {
        // Ensure the sales can only view their own leads
        if ($lead->assigned_to !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke lead ini.');
        }

        $lead->load([
            'source',
            'assignedUser',
            'creator',
            'deals.pipelineStage',
            'activities' => fn($q) => $q->with('user')->latest('activity_date'),
        ]);

        return view('sales.leads.show', compact('lead'));
    }

    public function qualify(QualifyLeadRequest $request, Lead $lead)
    {
        if ($lead->assigned_to !== auth()->id()) {
            abort(403);
        }

        $lead->update([
            'qualification' => $request->qualification,
            'status'        => $request->qualification === 'qualified' ? 'qualified' : $lead->status,
        ]);

        return back()->with('success', 'Lead berhasil di-qualify sebagai ' . config("beauty-crm.lead_qualifications.{$request->qualification}") . '.');
    }

    public function convert(Lead $lead, DealService $dealService)
    {
        if ($lead->assigned_to !== auth()->id()) {
            abort(403);
        }

        if ($lead->qualification !== 'qualified') {
            return back()->with('error', 'Hanya lead yang sudah qualified yang bisa di-convert ke deal.');
        }

        // Redirect to deal creation form
        return redirect()->route('sales.deals.create', $lead);
    }
}