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
    /**
     * Muncul di: Menu Sidebar -> "Leads" (/sales/leads)
     * Tampilan: resources/views/sales/leads/index.blade.php
     * Penjelasan: Menampilkan daftar calon pelanggan (leads) milik sales dengan filter status, sumber, dan kata kunci pencarian.
     */
    public function index(Request $request)
    {
        $leads = Lead::where('assigned_to', auth()->id())
            ->filterStatus($request->status)    // filter berdasarkan status lead
            ->filterSource($request->source)    // filter berdasarkan sumber lead
            ->filterQualification($request->qualification)    // filter berdasarkan kualifikasi lead
            ->search($request->search)    // filter berdasarkan pencarian lead
            ->with(['source', 'assignedUser', 'deals'])
            ->withCount('deals')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $sources = LeadSource::where('is_active', true)->get();

        return view('sales.leads.index', compact('leads', 'sources'));
    }

    /**
     * Muncul di: Halaman Leads -> Tombol "+ Tambah Lead" (/sales/leads/create)
     * Tampilan: resources/views/sales/leads/create.blade.php
     * Penjelasan: Menampilkan form untuk menginput data calon pelanggan baru.
     */
    public function create()
    {
        $sources = LeadSource::select('id', 'name')->where('is_active', true)->get();

        return view('sales.leads.create', compact('sources'));
    }

    /**
     * Muncul di: Form Tambah Lead -> Tombol "Simpan / Submit"
     * Penjelasan: Menyimpan data lead baru ke database dengan status awal 'new', lalu mengarahkan ke halaman detail lead.
     */
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

    /**
     * Muncul di: Halaman Leads -> Klik Nama Lead atau Aksi "Lihat Detail" (/sales/leads/{id})
     * Tampilan: resources/views/sales/leads/show.blade.php
     * Penjelasan: Menampilkan detail profil lead, riwayat aktivitas follow-up, serta deal terkait.
     */
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

    /**
     * Muncul di: Halaman Detail Lead -> Tombol / Dropdown "Qualify Lead"
     * Penjelasan: Memperbarui kualifikasi lead (Qualified, Unqualified, Nurturing). Jika Qualified, status lead otomatis berubah jadi 'qualified', jita unqualified atau no fit maka lead akan di arsipkan
     */
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

    /**
     * Muncul di: Halaman Detail Lead -> Tombol "Convert to Deal"
     * Penjelasan: Mengarahkan lead yang sudah 'Qualified' ke form pembuatan Deal baru di Pipeline Sales.
     */
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