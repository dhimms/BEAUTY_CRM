<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreActivityRequest;
use App\Http\Requests\Sales\UpdateActivityRequest;
use App\Models\Activity;

class ActivityController extends Controller
{
    /**
     * Muncul di: Halaman Detail Lead (`leads/show.blade.php`) & Detail Deal (`deals/show.blade.php`) -> Form/Tab "Tambah Aktivitas"
     * Penjelasan: Mencatat log aktivitas (Panggilan, Meeting, Catatan, Email) dan jadwal follow-up. Otomatis mengubah status lead 'new' menjadi 'contacted'.
     */
    public function store(StoreActivityRequest $request)
    {
        Activity::create([
            'user_id'          => auth()->id(),
            'activitable_type' => $request->getActivitableType(),
            'activitable_id'   => $request->activitable_id,
            'type'             => $request->type,
            'subject'          => $request->subject,
            'description'      => $request->description,
            'duration'         => $request->duration,
            'result'           => $request->result,
            'activity_date'    => $request->activity_date ?? now(),
            'follow_up_date'   => $request->follow_up_date,
            'follow_up_type'   => $request->follow_up_type,
            'follow_up_notes'  => $request->follow_up_notes,
            'follow_up_status' => $request->follow_up_date ? 'pending' : 'done',
        ]);

        // jika lead statusnya new, ubah ke contacted
        if ($request->activitable_type === 'lead') {
            $lead = \App\Models\Lead::find($request->activitable_id);
            if ($lead && $lead->status === 'new') {
                $lead->update(['status' => 'contacted']);
            }
        }

        return back()->with('success', 'Aktivitas berhasil dicatat.');
    }

    /**
     * Muncul di: Timeline Aktivitas (Detail Lead/Deal) -> Tombol "Edit Aktivitas"
     * Penjelasan: Memperbarui isi catatan, tipe, atau jadwal follow-up pada aktivitas yang dibuat oleh sales.
     */
    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        if ($activity->user_id !== auth()->id() && !auth()->user()->hasRole(['Admin', 'Manager'])) {
            abort(403);
        }

        $activity->update($request->validated());

        return back()->with('success', 'Aktivitas berhasil diperbarui.');
    }

    /**
     * Muncul di: Timeline Aktivitas (Detail Lead/Deal) -> Tombol Hapus (Icon Sampah)
     * Penjelasan: Menghapus data log aktivitas yang pernah dicatat oleh sales.
     */
    public function destroy(Activity $activity)
    {
        if ($activity->user_id !== auth()->id() && !auth()->user()->hasRole(['Admin', 'Manager'])) {
            abort(403);
        }

        $activity->delete();

        return back()->with('success', 'Aktivitas berhasil dihapus.');
    }

    /**
     * Muncul di: Dashboard Sales (Widget "Today's Follow-ups") & Detail Lead/Deal -> Checkbox/Tombol "Tandai Selesai"
     * Penjelasan: Mengubah status follow-up aktivitas dari 'pending' menjadi 'done'.
     */
    public function completeFollowUp(Activity $activity)
    {
        if ($activity->user_id !== auth()->id() && !auth()->user()->hasRole(['Admin', 'Manager'])) {
            abort(403);
        }

        $activity->update(['follow_up_status' => 'done']);

        return back()->with('success', 'Follow-up ditandai selesai.');
    }
}