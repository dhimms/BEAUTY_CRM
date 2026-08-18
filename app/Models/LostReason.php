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

    // casts disini berfungsi untuk mengubah tipe data dari kolom is_active menjadi boolean
    // sehingga saat di cek di view akan otomatis menjadi true atau false bukan 0 atau 1
    // dan juga berfungsi untuk mengubah tipe data dari kolom created_at dan updated_at menjadi datetime
    protected function casts(): array 
    {
        return [
            'is_active' => 'boolean',
        ];
    }  

    // Relasi ke tabel Deals (daftar deal yang gagal dengan alasan ini)
    // method untuk mendefinisikan relasi one to many dari lost reason ke deal
    // artinya satu lost reason bisa memiliki banyak deal 
    public function deals()
    {
        return $this->hasMany(Deal::class);
    }
}