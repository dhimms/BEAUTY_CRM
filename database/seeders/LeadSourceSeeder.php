<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            ['name' => 'Website', 'icon' => 'globe', 'color' => '#3B82F6', 'description' => 'Lead dari website perusahaan'],
            ['name' => 'WhatsApp', 'icon' => 'message-circle', 'color' => '#22C55E', 'description' => 'Lead dari WhatsApp Business'],
            ['name' => 'Email', 'icon' => 'mail', 'color' => '#8B5CF6', 'description' => 'Lead dari email marketing'],
            ['name' => 'Referral', 'icon' => 'users', 'color' => '#F59E0B', 'description' => 'Lead dari referensi pelanggan'],
            ['name' => 'Social Media', 'icon' => 'share-2', 'color' => '#EC4899', 'description' => 'Lead dari Instagram, TikTok, Facebook'],
            ['name' => 'Walk-in', 'icon' => 'map-pin', 'color' => '#14B8A6', 'description' => 'Lead yang datang langsung ke tempat'],
        ];

        foreach ($sources as $source) {
            LeadSource::firstOrCreate(
                ['name' => $source['name']],
                $source
            );
        }
    }
}