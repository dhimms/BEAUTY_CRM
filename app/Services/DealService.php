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
     * Dipanggil oleh: DealController@store (Submit Form Buat Deal)
     * Penjelasan: Membuat data Deal baru di pipeline stage awal dan mengubah status Lead dari qualified menjadi 'converted'.
     */
    public function createFromLead(Lead $lead, array $data): Deal
    {
        return DB::transaction(function () use ($lead, $data) {
            // Mengambil stage pertama dari pipeline jika Sales belum memilih stage
            $firstStage = PipelineStage::ordered()->first();

            // Membuat Deal baru berdasarkan data Lead dan input dari Sales
            $deal = Deal::create([
                'lead_id'             => $lead->id,
                'name'                => $data['name'],
                'value'               => 0,
                'pipeline_stage_id'   => $data['pipeline_stage_id'] ?? $firstStage->id,
                'status'              => 'open',
                'expected_close_date' => $data['expected_close_date'] ?? null,
                'assigned_to'         => $lead->assigned_to,
                'created_by'          => auth()->id(),
            ]);

            // Mengubah status Lead menjadi converted karena sudah dibuat menjadi Deal
            $lead->update(['status' => 'converted']);

            return $deal;
        });
    }

    /**
     * Dipanggil oleh: DealController@moveStage (Tombol "Pindah Stage Next")
     * Penjelasan: Menggeser stage deal 1 tingkat ke depan dalam pipeline.
     */
    public function moveToNextStage(Deal $deal): Deal
    {
        // Mengambil urutan stage tempat Deal berada sekarang
        $currentOrder = $deal->pipelineStage->order;

        // Mencari stage berikutnya berdasarkan urutan pipeline
        $nextStage = PipelineStage::where('order', '>', $currentOrder)
            ->ordered()
            ->first();

        // Jika tidak ada stage berikutnya, berarti Deal sudah di stage terakhir
        if (!$nextStage) {
            throw new \Exception('Deal sudah berada di stage terakhir.');
        }

        // Mengubah Deal ke stage berikutnya
        $deal->update([
            'pipeline_stage_id' => $nextStage->id
        ]);

        // Mengambil kembali data Deal beserta stage terbarunya
        return $deal->fresh('pipelineStage');
    }

    /**
     * Dipanggil oleh: DealController@moveStage (Drag & Drop di Kanban Board `pipeline.blade.php`)
     * Penjelasan: Memindahkan stage deal langsung ke stageId tertentu hasil drag and drop.
     */
    public function moveToStage(Deal $deal, int $stageId): Deal
    {
        // Mencari stage berdasarkan ID yang dipilih
        $stage = PipelineStage::findOrFail($stageId);

        // Mengubah stage Deal ke stage yang dipilih
        $deal->update([
            'pipeline_stage_id' => $stage->id
        ]);

        // Mengambil kembali data Deal beserta stage terbaru
        return $deal->fresh('pipelineStage');
    }

    /**
     * Dipanggil oleh: DealController@close (Tombol "Mark as Won")
     * Penjelasan: Mengubah status deal menjadi 'won', mencatat closed_at, dan otomatis membuatkan data Customer baru dari Lead.
     */
    public function closeWon(Deal $deal): Deal
    {
        return DB::transaction(function () use ($deal) {

            // Mengubah status Deal menjadi WON dan mencatat waktu Deal ditutup
            $deal->update([
                'status'    => 'won',
                'closed_at' => now(),
            ]);

            // Mengambil Lead yang berhubungan dengan Deal
            $lead = $deal->lead;

            // Mengecek apakah Customer dari Lead tersebut sudah pernah dibuat
            $existingCustomer = Customer::where('lead_id', $lead->id)->first();

            // Jika Customer belum ada, maka buat Customer baru dari data Lead
            if (!$existingCustomer) {
                Customer::create([
                    'lead_id' => $lead->id,
                    'user_id' => null, // CS akan ditentukan nanti
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
     * Dipanggil oleh: DealController@close (Tombol "Mark as Lost")
     * Penjelasan: Mengubah status deal menjadi 'lost', mencatat closed_at, alasan lost (lost_reason_id), dan catatan kekalahan.
     */
    public function closeLost(
        Deal $deal,
        int $lostReasonId,
        ?string $lostNotes = null
    ): Deal {
        // Mengubah status Deal menjadi LOST dan menyimpan alasan kehilangan
        $deal->update([
            'status'         => 'lost',
            'closed_at'      => now(),
            'lost_reason_id' => $lostReasonId,
            'lost_notes'     => $lostNotes,
        ]);

        return $deal;
    }
}