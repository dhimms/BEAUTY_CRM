<?php

namespace Database\Seeders;

use App\Models\PipelineStage;
use Illuminate\Database\Seeder;

class PipelineStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            ['name' => 'Prospecting', 'color' => '#3B82F6', 'order' => 1, 'probability' => 20],
            ['name' => 'Proposal', 'color' => '#8B5CF6', 'order' => 2, 'probability' => 50],
            ['name' => 'Negotiation', 'color' => '#F59E0B', 'order' => 3, 'probability' => 75],
            ['name' => 'Closing', 'color' => '#6B7280', 'order' => 4, 'probability' => 90],
        ];

        foreach ($stages as $stage) {
            PipelineStage::firstOrCreate(
                ['name' => $stage['name']],
                $stage
            );
        }
    }
}