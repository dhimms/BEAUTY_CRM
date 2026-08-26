<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\CloseDealRequest;
use App\Http\Requests\Sales\StoreDealRequest;
use App\Http\Requests\Sales\UpdateDealRequest;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\LostReason;
use App\Models\PipelineStage;
use App\Models\User;
use App\Notifications\DealWonNotification;
use App\Services\DealService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class DealController extends Controller
{
    public function __construct(
        protected DealService $dealService
    ) {}

    /**
     * Muncul di: Menu Sidebar -> "Pipeline" (/sales/pipeline)
     * Tampilan: resources/views/sales/deals/pipeline.blade.php
     * Penjelasan: Menampilkan board kanban deal berdasarkan tahapan (stage) pipeline sales.
     */
    public function pipeline(Request $request)
    {
        $stages = PipelineStage::ordered()
            ->with(['deals' => function ($q) {
                $q->where('assigned_to', auth()->id())
                  ->where('status', 'open')
                  ->with(['lead', 'assignedUser'])
                  ->orderBy('updated_at', 'desc');
            }])
            ->get();

        return view('sales.deals.pipeline', compact('stages'));
    }

    /**
     * Muncul di: Menu Sidebar -> "Deals" / Tombol "List View" di Halaman Pipeline (/sales/deals)
     * Tampilan: resources/views/sales/deals/index.blade.php
     * Penjelasan: Menampilkan daftar deal dalam bentuk tabel dengan filter stage, status, dan pencarian.
     */
    public function index(Request $request)
    {
        $deals = Deal::where('assigned_to', auth()->id())
            ->where('status', 'open') // Hanya tampilkan deal yang masih berjalan (open)
            ->filterStage($request->stage)
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('lead', fn($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->with(['lead.latestActivity', 'pipelineStage', 'assignedUser'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stages = PipelineStage::ordered()->get();

        return view('sales.deals.index', compact('deals', 'stages'));
    }

    /**
     * Muncul di: Halaman Detail Lead -> Tombol "Convert to Deal" (/sales/deals/create/{lead})
     * Tampilan: resources/views/sales/deals/create.blade.php
     * Penjelasan: Menampilkan form pembuatan deal baru khusus untuk lead yang sudah berstatus Qualified.
     */
    public function create(Lead $lead)
    {
        if ($lead->assigned_to !== auth()->id()) {
            abort(403);
        }

        if ($lead->qualification !== 'qualified') {
            return redirect()->route('sales.leads.show', $lead)
                ->with('error', 'Lead harus di-qualify terlebih dahulu.');
        }

        $stages = PipelineStage::ordered()->get();

        return view('sales.deals.create', compact('lead', 'stages'));
    }

    /**
     * Muncul di: Form Buat Deal -> Tombol "Simpan / Create Deal"
     * Penjelasan: Menyimpan deal baru dan mengubah status lead terkait menjadi 'converted'.
     */
    public function store(StoreDealRequest $request)
    {
        $lead = Lead::findOrFail($request->lead_id);

        if ($lead->assigned_to !== auth()->id()) {
            abort(403);
        }

        $deal = $this->dealService->createFromLead($lead, $request->validated());

        return redirect()->route('sales.deals.show', $deal)
            ->with('success', 'Deal berhasil dibuat!');
    }

    /**
     * Muncul di: Klik Card Deal pada Kanban / Nama Deal pada Tabel List View (/sales/deals/{id})
     * Tampilan: resources/views/sales/deals/show.blade.php
     * Penjelasan: Menampilkan detail deal, estimasi nilai, tahapan stage saat ini, dan log aktivitas terkait.
     */
    public function show(Deal $deal)
    {
        if ($deal->assigned_to !== auth()->id() && !auth()->user()->hasRole(['Admin', 'Manager'])) {
            abort(403);
        }

        $deal->load([
            'lead.source',
            'pipelineStage',
            'assignedUser',
            'creator',
            'lostReason',
            'activities' => fn($q) => $q->with('user')->latest('activity_date'),
        ]);

        $stages = PipelineStage::ordered()->get();
        $lostReasons = LostReason::where('is_active', true)->get();

        return view('sales.deals.show', compact('deal', 'stages', 'lostReasons'));
    }

    /**
     * Muncul di: Halaman Detail Deal -> Modal / Form "Edit Deal"
     * Penjelasan: Memperbarui informasi deal (nama deal, estimasi nilai deal, tanggal penutupan).
     */
    public function update(UpdateDealRequest $request, Deal $deal)
    {
        if ($deal->assigned_to !== auth()->id() && !auth()->user()->hasRole(['Admin', 'Manager'])) {
            abort(403);
        }

        $deal->update($request->validated());

        return back()->with('success', 'Deal berhasil diperbarui.');
    }

    /**
     * Muncul di: Kanban Board (Drag & Drop Card Deal ke Kolom Lain) atau Detail Deal (Tombol "Pindah Stage")
     * Penjelasan: Mengubah tahapan stage pada deal (via AJAX saat drag & drop di kanban board).
     */
    public function moveStage(Request $request, Deal $deal)
    {
        if ($deal->assigned_to !== auth()->id() && !auth()->user()->hasRole(['Admin', 'Manager'])) {
            abort(403);
        }

        if ($deal->status !== 'open') {
            return response()->json(['error' => 'Deal sudah closed.'], 422);
        }

        try {
            if ($request->has('stage_id')) {
                $deal = $this->dealService->moveToStage($deal, $request->stage_id);
            } else {
                $deal = $this->dealService->moveToNextStage($deal);
            }

            return response()->json([
                'success' => true,
                'message' => 'Deal dipindahkan ke stage: ' . $deal->pipelineStage->name,
                'stage'   => $deal->pipelineStage,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Muncul di: Halaman Detail Deal -> Tombol "Mark as Won" atau "Mark as Lost"
     * Penjelasan: Menutup deal. Jika WON: otomatis buat Customer baru dan kirim notifikasi ke Admin & Manager. Jika LOST: simpan alasan kekalahan.
     */
    public function close(CloseDealRequest $request, Deal $deal)
    {
        if ($deal->assigned_to !== auth()->id() && !auth()->user()->hasRole(['Admin', 'Manager'])) {
            abort(403);
        }

        if ($deal->status !== 'open') {
            return back()->with('error', 'Deal sudah closed.');
        }

        if ($request->outcome === 'won') {
            $this->dealService->closeWon($deal, $request->product_name, $request->value);

            $deal->load('assignedUser');
            if (config('beauty-crm.notify_won_deal')) {
                $admins = User::role('Admin')->get();
                Notification::send($admins, new DealWonNotification($deal));
            }

            return redirect()->route('sales.deals.show', $deal)
                ->with('success', 'Deal ditandai sebagai WON! Customer baru telah dibuat.');
        } else {
            $this->dealService->closeLost($deal, $request->lost_reason_id, $request->lost_notes);
            return redirect()->route('sales.deals.show', $deal)
                ->with('success', 'Deal ditandai sebagai LOST.');
        }
    }

    public function blast(Request $request)
    {
        $request->validate([
            'deal_ids' => 'required|array',
            'deal_ids.*' => 'exists:deals,id',
            'channel' => 'required|in:whatsapp,email',
            'message' => 'required|string|max:1000'
        ]);

        $count = $this->dealService->blastMessage(
            $request->deal_ids,
            $request->channel,
            $request->message
        );

        return back()->with('success', "Pesan blast berhasil dikirim ke $count lead via " . ucfirst($request->channel));
    }
}