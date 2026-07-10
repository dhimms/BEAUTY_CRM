<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;
use Illuminate\Support\Facades\DB;

class DealService
{
    /**
     * Create a new deal from a qualified lead.
     */
    public function createFromLead(Lead $lead, array $data): Deal
    {
        return DB::transaction(function () use ($lead, $data) {
            // Get the first pipeline stage if not specified
            $firstStage = PipelineStage::ordered()->first();

            $deal = Deal::create([
                'lead_id'            => $lead->id,
                'name'               => $data['name'],
                'value'              => $data['value'],
                'pipeline_stage_id'  => $data['pipeline_stage_id'] ?? $firstStage->id,
                'status'             => 'open',
                'expected_close_date'=> $data['expected_close_date'] ?? null,
                'assigned_to'        => $lead->assigned_to,
                'created_by'         => auth()->id(),
            ]);

            // Update lead status to converted
            $lead->update(['status' => 'converted']);

            return $deal;
        });
    }

    /**
     * Move a deal to the next pipeline stage.
     */
    public function moveToNextStage(Deal $deal): Deal
    {
        $currentOrder = $deal->pipelineStage->order;
        $nextStage = PipelineStage::where('order', '>', $currentOrder)
            ->ordered()
            ->first();

        if (!$nextStage) {
            throw new \Exception('Deal sudah berada di stage terakhir.');
        }

        $deal->update(['pipeline_stage_id' => $nextStage->id]);

        return $deal->fresh('pipelineStage');
    }

    /**
     * Move a deal to a specific stage (for drag & drop).
     */
    public function moveToStage(Deal $deal, int $stageId): Deal
    {
        $stage = PipelineStage::findOrFail($stageId);
        $deal->update(['pipeline_stage_id' => $stage->id]);

        return $deal->fresh('pipelineStage');
    }

    /**
     * Close a deal as Won — creates a customer from the lead.
     */
    public function closeWon(Deal $deal): Deal
    {
        return DB::transaction(function () use ($deal) {
            $deal->update([
                'status'    => 'won',
                'closed_at' => now(),
            ]);

            // Create customer from lead (if not already existing)
            $lead = $deal->lead;
            $existingCustomer = Customer::where('lead_id', $lead->id)->first();

            if (!$existingCustomer) {
                Customer::create([
                    'lead_id' => $lead->id,
                    'user_id' => null, // CS will be assigned later
                    'name'    => $lead->name,
                    'email'   => $lead->email,
                    'phone'   => $lead->phone,
                    'address' => $lead->address,
                    'status'  => 'active',
                    'notes'   => "Converted from Deal: {$deal->name}",
                ]);
            }

            return $deal;
        });
    }

    /**
     * Close a deal as Lost — requires lost_reason_id and lost_notes.
     */
    public function closeLost(Deal $deal, int $lostReasonId, ?string $lostNotes = null): Deal
    {
        $deal->update([
            'status'         => 'lost',
            'closed_at'      => now(),
            'lost_reason_id' => $lostReasonId,
            'lost_notes'     => $lostNotes,
        ]);

        return $deal;
    }
}
