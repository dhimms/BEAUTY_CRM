<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model LostReason (Alasan Deal Gagal / Lost)
 * Digunakan di: DealController, DealService, dan Modal Close Lost Views.
 * Fungsi: Mengelola daftar opsi alasan kenapa sebuah transaksi deal gagal (Harga kemahalan, Tidak respon, Pilih kompetitor, dll).
 */
class LostReason extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relasi ke tabel Deals (daftar deal yang gagal dengan alasan ini)
    public function deals()
    {
        return $this->hasMany(Deal::class);
    }
}