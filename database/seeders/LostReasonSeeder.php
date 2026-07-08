<?php

namespace Database\Seeders;

use App\Models\LostReason;
use Illuminate\Database\Seeder;

class LostReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            ['name' => 'Harga Terlalu Mahal', 'description' => 'Pelanggan merasa harga di atas budget mereka'],
            ['name' => 'Pilih Kompetitor', 'description' => 'Pelanggan memilih produk/layanan kompetitor'],
            ['name' => 'Tidak Jadi', 'description' => 'Pelanggan membatalkan rencana pembelian'],
            ['name' => 'Budget Ditunda', 'description' => 'Pelanggan menunda pembelian ke periode berikutnya'],
            ['name' => 'Tidak Ada Respon', 'description' => 'Pelanggan tidak merespon setelah beberapa kali follow-up'],
            ['name' => 'Tidak Sesuai Kebutuhan', 'description' => 'Produk/layanan tidak sesuai dengan kebutuhan pelanggan'],
            ['name' => 'Lokasi Tidak Terjangkau', 'description' => 'Jarak lokasi terlalu jauh bagi pelanggan'],
        ];

        foreach ($reasons as $reason) {
            LostReason::firstOrCreate(
                ['name' => $reason['name']],
                $reason
            );
        }
    }
}