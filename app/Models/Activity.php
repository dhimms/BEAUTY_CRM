<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Activity (Log Aktivitas & Follow-up)
 * Digunakan di: ActivityController, DashboardController, dan Detail Lead/Deal Views.
 * Fungsi: Mengelola catatan log aktivitas komunikasi (Call, Meeting, WhatsApp, Email) & penjadwalan follow-up.
 */
class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'activitable_type',
        'activitable_id',
        'type',
        'subject',
        'description',
        'duration',
        'result',
        'activity_date',
        'follow_up_date',
        'follow_up_type',
        'follow_up_notes',
        'follow_up_status',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'datetime',
            'follow_up_date' => 'date',
        ];
    }

    // ─── Relationships (Relasi Antar Tabel) ─────────

    // Relasi ke tabel User (sales pembuat aktivitas)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Polymorphic (bisa ke Lead, Deal, atau Customer)
    public function activitable()
    {
        return $this->morphTo();
    }

    // ─── Scopes (Filter Query Data) ──────────────────

    // Filter daftar follow-up yang masih pending untuk hari ini & mendatang (dipakai di widget Dashboard Sales)
    public function scopePendingFollowUps($query)
    {
        return $query->where('follow_up_status', 'pending')
            ->whereNotNull('follow_up_date')
            ->where('follow_up_date', '>=', now()->toDateString())
            ->orderBy('follow_up_date');
    }

    // Filter daftar follow-up yang sudah terlewat / terlambat (overdue)
    public function scopeOverdueFollowUps($query)
    {
        return $query->where('follow_up_status', 'pending')
            ->whereNotNull('follow_up_date')
            ->where('follow_up_date', '<', now()->toDateString());
    }

    // ─── Accessors / Helpers (Tampilan Badge & Icon UI)

    // Menentukan warna badge jenis aktivitas (call = blue, whatsapp = emerald, meeting = amber, email = purple)
    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'call' => 'blue',
            'whatsapp' => 'emerald',
            'email' => 'purple',
            'meeting' => 'amber',
            'note' => 'gray',
            default => 'gray',
        };
    }

    // Menentukan nama icon SVG untuk jenis aktivitas di tampilan timeline
    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'call' => 'phone',
            'whatsapp' => 'message-circle',
            'email' => 'mail',
            'meeting' => 'users',
            'note' => 'file-text',
            default => 'activity',
        };
    }
}