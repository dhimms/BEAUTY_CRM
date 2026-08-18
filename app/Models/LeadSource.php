<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model LeadSource (Sumber Asal Lead)
 * Digunakan di: LeadController, Admin Controller, dan Form Tambah Lead Views.
 * Fungsi: Mengelola data opsi dari mana calon pelanggan berasal (Instagram, WhatsApp, Website, Event, Referensi).
 */
class LeadSource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'color',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relasi ke tabel Leads (daftar lead yang didapat dari sumber ini)
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    // Scope: Mengambil sumber lead yang statusnya sedang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}