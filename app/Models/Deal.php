<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Deal (Peluang Penjualan / Transaksi Pipeline)
 * Digunakan di: DealController, DashboardController, DealService, ReportService, dan Pipeline Views.
 * Fungsi: Mengelola data peluang transaksi sales (nilai deal, stage pipeline, status open/won/lost).
 */
class Deal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'name',
        'value',
        'pipeline_stage_id',
        'status',
        'lost_reason_id',
        'lost_notes',
        'expected_close_date',
        'closed_at',
        'assigned_to',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'expected_close_date' => 'date',
            'closed_at' => 'datetime',
        ];
    }

    // ─── Relationships (Relasi Antar Tabel) ─────────

    // Relasi ke tabel Lead (calon pelanggan asal deal)
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    // Relasi ke tabel PipelineStage (tahapan stage kanban: Prospect, Proposal, dll)
    public function pipelineStage()
    {
        return $this->belongsTo(PipelineStage::class);
    }

    // Relasi ke tabel LostReason (alasan jika deal berstatus lost/gagal)
    public function lostReason()
    {
        return $this->belongsTo(LostReason::class);
    }

    // Relasi ke tabel User (sales penanggung jawab)
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relasi ke tabel User (pembuat data deal)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke tabel Activities (log aktivitas follow-up pada deal)
    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitable');
    }

    // ─── Scopes (Filter Query Data) ──────────────────

    // Filter deal yang statusnya masih 'open' (sedang berjalan di pipeline)
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    // Filter deal yang berhasil dimenangkan ('won')
    public function scopeWon($query)
    {
        return $query->where('status', 'won');
    }

    // Filter deal yang gagal ('lost')
    public function scopeLost($query)
    {
        return $query->where('status', 'lost');
    }

    // Filter deal berdasarkan status tertentu (open, won, lost)
    public function scopeFilterStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    // Filter deal berdasarkan ID stage pipeline
    public function scopeFilterStage($query, ?int $stageId)
    {
        return $stageId ? $query->where('pipeline_stage_id', $stageId) : $query;
    }

    // ─── Accessors / Helpers (Format Tampilan UI) ───

    // Menghitung bobot perkiraan pendapatan deal berdasarkan probabilitas stage (dipakai di Forecast Laporan)
    public function getWeightedValueAttribute(): float
    {
        $probability = $this->pipelineStage?->probability ?? 0;
        return $this->value * ($probability / 100);
    }

    // Mengubah format nilai rupiah (contoh: "Rp 5.000.000") untuk tampilan kartu deal
    public function getFormattedValueAttribute(): string
    {
        return 'Rp ' . number_format($this->value, 0, ',', '.');
    }

    // Menentukan warna badge status deal di Blade view (open = blue, won = emerald, lost = red)
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'blue',
            'won' => 'emerald',
            'lost' => 'red',
            default => 'gray',
        };
    }
}