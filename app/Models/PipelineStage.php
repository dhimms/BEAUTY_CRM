<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model PipelineStage (Tahapan Kolom Kanban Sales)
 * Digunakan di: DealController, DealService, ReportService, dan Kanban Board Views.
 * Fungsi: Mengelola daftar kolom tahapan alur penjualan (Prospect, Qualification, Proposal, Negotiation, Closing).
 */
class PipelineStage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'color',
        'order',
        'probability',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'probability' => 'integer',
        ];
    }

    // Relasi ke tabel Deals (daftar deal yang ada di stage ini)
    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    // Scope: Mengurutkan stage berdasarkan urutan kolom (order)
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}