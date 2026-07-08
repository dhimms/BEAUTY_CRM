<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DealRequest;
use App\Models\Deal;
use App\Models\LostReason;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index(Request $request)
    {
        $deals = Deal::query()
            ->with(['lead', 'pipelineStage', 'assignedUser'])
            ->when($request->search, fn($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('name', 'like', "%$v%")
                  ->orWhereHas('lead', fn($q) => $q->where('name', 'like', "%$v%"));
            }))
            ->filterStatus($request->status)
            ->filterStage($request->stage)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stages = PipelineStage::ordered()->get();

        return view('admin.deals.index', compact('deals', 'stages'));
    }

    public function show(Deal $deal)
    {
        $deal->load(['lead', 'pipelineStage', 'lostReason', 'assignedUser', 'activities.user']);
        return view('admin.deals.show', compact('deal'));
    }

    public function edit(Deal $deal)
    {
        $stages      = PipelineStage::ordered()->get();
        $lostReasons = LostReason::all();
        $salesList   = User::role('Sales')->where('is_active', true)->get();
        return view('admin.deals.edit', compact('deal', 'stages', 'lostReasons', 'salesList'));
    }

    public function update(DealRequest $request, Deal $deal)
    {
        $data = $request->validated();
        if ($data['status'] === 'won' && !$deal->closed_at) {
            $data['closed_at'] = now();
        } elseif ($data['status'] === 'lost' && !$deal->closed_at) {
            $data['closed_at'] = now();
        }
        $deal->update($data);
        return redirect()->route('admin.deals.show', $deal)
            ->with('success', "Deal {$deal->name} berhasil diperbarui.");
    }

    public function destroy(Deal $deal)
    {
        $name = $deal->name;
        $deal->delete();
        return redirect()->route('admin.deals.index')
            ->with('success', "Deal {$name} berhasil dihapus.");
    }
}
