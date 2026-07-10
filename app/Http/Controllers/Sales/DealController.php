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
use App\Services\DealService;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function __construct(
        protected DealService $dealService
    ) {}

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

    public function index(Request $request)
    {
        $deals = Deal::where('assigned_to', auth()->id())
            ->filterStatus($request->status)
            ->filterStage($request->stage)
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('lead', fn($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->with(['lead', 'pipelineStage', 'assignedUser'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stages = PipelineStage::ordered()->get();

        return view('sales.deals.index', compact('deals', 'stages'));
    }

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

    public function show(Deal $deal)
    {
        if ($deal->assigned_to !== auth()->id()) {
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

    public function update(UpdateDealRequest $request, Deal $deal)
    {
        if ($deal->assigned_to !== auth()->id()) {
            abort(403);
        }

        $deal->update($request->validated());

        return back()->with('success', 'Deal berhasil diperbarui.');
    }

    public function moveStage(Request $request, Deal $deal)
    {
        if ($deal->assigned_to !== auth()->id()) {
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

    public function close(CloseDealRequest $request, Deal $deal)
    {
        if ($deal->assigned_to !== auth()->id()) {
            abort(403);
        }

        if ($deal->status !== 'open') {
            return back()->with('error', 'Deal sudah closed.');
        }

        if ($request->outcome === 'won') {
            $this->dealService->closeWon($deal);
            return redirect()->route('sales.deals.show', $deal)
                ->with('success', 'Deal ditandai sebagai WON! Customer baru telah dibuat.');
        } else {
            $this->dealService->closeLost($deal, $request->lost_reason_id, $request->lost_notes);
            return redirect()->route('sales.deals.show', $deal)
                ->with('success', 'Deal ditandai sebagai LOST.');
        }
    }
}