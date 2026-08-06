<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LostReason;
use App\Models\PipelineStage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyCustomerSeeder extends Seeder
{
    // ─── Beauty Clinic themed names ──────────────────────
    private array $firstNames = [
        'Sari', 'Dewi', 'Putri', 'Ayu', 'Maya', 'Rina', 'Lina', 'Nita', 'Dian', 'Fitri',
        'Ani', 'Sri', 'Wati', 'Yuni', 'Ratna', 'Indah', 'Mega', 'Tika', 'Rini', 'Siti',
        'Nurul', 'Eka', 'Tri', 'Wulan', 'Kartini', 'Laras', 'Anisa', 'Zahra', 'Bella', 'Citra',
        'Diana', 'Eva', 'Farah', 'Gita', 'Hana', 'Ira', 'Julia', 'Karin', 'Lisa', 'Maria',
        'Nina', 'Olivia', 'Puspita', 'Qory', 'Rosa', 'Sarah', 'Tiara', 'Ulfa', 'Vera', 'Winda',
        'Amanda', 'Bunga', 'Cantika', 'Dinda', 'Elsa', 'Fiona', 'Galuh', 'Hesti', 'Intan', 'Jasmine',
        'Kirana', 'Lutfiah', 'Melati', 'Nabila', 'Oktavia', 'Priska', 'Queena', 'Rahma', 'Salma', 'Tasya',
    ];

    private array $lastNames = [
        'Pratiwi', 'Susanti', 'Rahayu', 'Handayani', 'Lestari', 'Wulandari', 'Sari', 'Anggraini',
        'Permata', 'Utami', 'Maharani', 'Kusuma', 'Purnama', 'Cahyani', 'Hartono', 'Santoso',
        'Wijaya', 'Suryani', 'Adriani', 'Budiman', 'Gunawan', 'Hidayat', 'Iskandar', 'Kusnadi',
        'Mulyani', 'Nugraha', 'Oktaviani', 'Prabowo', 'Saputri', 'Wardani', 'Yuniar', 'Azzahra',
    ];

    private array $cities = [
        'Jakarta Selatan', 'Jakarta Pusat', 'Jakarta Barat', 'Jakarta Utara', 'Jakarta Timur',
        'Bandung', 'Surabaya', 'Tangerang', 'Bekasi', 'Depok', 'Bogor', 'Semarang',
        'Yogyakarta', 'Malang', 'Medan', 'Makassar', 'Bali', 'Solo', 'Palembang', 'Batam',
    ];

    private array $streets = [
        'Jl. Sudirman', 'Jl. Gatot Subroto', 'Jl. Thamrin', 'Jl. Kuningan', 'Jl. Rasuna Said',
        'Jl. Kemang Raya', 'Jl. Senopati', 'Jl. Blok M', 'Jl. Fatmawati', 'Jl. Pejaten',
        'Jl. Pondok Indah', 'Jl. Cipete', 'Jl. Ampera', 'Jl. Radio Dalam', 'Jl. Panglima Polim',
        'Jl. Wolter Monginsidi', 'Jl. Wijaya', 'Jl. Melawai', 'Jl. Dharmawangsa', 'Jl. Brawijaya',
    ];

    private array $beautyTreatments = [
        'Facial Treatment Basic', 'Facial Treatment Premium', 'Chemical Peeling', 'Microdermabrasion',
        'Botox Treatment', 'Filler Treatment', 'Laser Whitening', 'Acne Treatment Package',
        'Anti-Aging Package', 'Hair Removal Laser', 'Body Slimming', 'Mesotherapy',
        'PRP Treatment', 'Threadlift', 'Hydrafacial', 'LED Light Therapy',
        'Vitamin C Injection', 'Glutathione Drip', 'Diamond Peel', 'Gold Facial',
        'Body Scrub Premium', 'Aromatherapy Massage', 'V-Shape Treatment', 'Skin Booster',
    ];

    private array $tags = [
        ['VIP', 'Regular'],
        ['Premium', 'Loyal'],
        ['New Customer'],
        ['VIP', 'Premium'],
        ['Regular'],
        ['Loyal', 'VIP'],
        ['Premium'],
        ['VIP'],
        null,
        null,
    ];

    private array $activityTypes = ['call', 'whatsapp', 'email', 'meeting', 'note'];

    private array $activitySubjects = [
        'call' => ['Follow-up konsultasi', 'Konfirmasi jadwal', 'Reminder treatment', 'Info promo baru', 'Follow-up hasil treatment'],
        'whatsapp' => ['Kirim katalog treatment', 'Info promo bulan ini', 'Reminder jadwal besok', 'Kirim before-after photos', 'Follow-up kepuasan pelanggan'],
        'email' => ['Kirim penawaran paket', 'Newsletter bulanan', 'Invoice treatment', 'Kirim hasil konsultasi', 'Promo membership'],
        'meeting' => ['Konsultasi awal', 'Review treatment plan', 'Diskusi paket custom', 'Follow-up hasil treatment', 'Konsultasi lanjutan'],
        'note' => ['Catatan preferensi client', 'Catatan alergi kulit', 'Update kondisi kulit', 'Request khusus client', 'Catatan follow-up'],
    ];

    public function run(): void
    {
        $this->command->info('🌸 Generating dummy Beauty Clinic data...');

        // ─── 1. Create additional sales users ────────────
        $salesUsers = $this->createSalesUsers();
        $this->command->info('✅ Created ' . count($salesUsers) . ' sales users');

        // ─── 2. Create additional CS users ───────────────
        $csUsers = $this->createCSUsers();
        $this->command->info('✅ Created ' . count($csUsers) . ' CS users');

        // ─── 3. Get reference data ───────────────────────
        $leadSources = LeadSource::pluck('id')->toArray();
        $pipelineStages = PipelineStage::orderBy('order')->get();
        $lostReasons = LostReason::pluck('id')->toArray();

        if (empty($leadSources) || $pipelineStages->isEmpty()) {
            $this->command->error('❌ Please run the base seeders first (LeadSourceSeeder, PipelineStageSeeder, LostReasonSeeder)');
            return;
        }

        // ─── 4. Generate 500 records spread across dates ─
        $now = Carbon::now();
        $totalRecords = 500;

        // Distribution: ensure data for every filter period
        $dateDistribution = $this->generateDateDistribution($now, $totalRecords);

        $bar = $this->command->getOutput()->createProgressBar($totalRecords);
        $bar->start();

        $customerCount = 0;
        foreach ($dateDistribution as $createdAt) {
            $salesUser = $salesUsers[array_rand($salesUsers)];
            $csUser = $csUsers[array_rand($csUsers)];
            $sourceId = $leadSources[array_rand($leadSources)];

            $firstName = $this->firstNames[array_rand($this->firstNames)];
            $lastName = $this->lastNames[array_rand($this->lastNames)];
            $fullName = $firstName . ' ' . $lastName;
            $phone = '08' . rand(11, 99) . rand(1000000, 9999999);
            $email = strtolower($firstName) . '.' . strtolower($lastName) . rand(1, 999) . '@gmail.com';
            $city = $this->cities[array_rand($this->cities)];
            $street = $this->streets[array_rand($this->streets)];
            $address = $street . ' No. ' . rand(1, 200) . ', ' . $city;

            // ─── Create Lead ─────────────────────────────
            $leadStatus = $this->getRandomLeadStatus();
            $qualification = $this->getQualificationForStatus($leadStatus);

            $lead = Lead::create([
                'name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'lead_source_id' => $sourceId,
                'assigned_to' => $salesUser->id,
                'status' => $leadStatus,
                'qualification' => $qualification,
                'notes' => $this->generateLeadNotes(),
                'created_by' => $salesUser->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHours(rand(1, 48)),
            ]);

            // ─── Create Deal (for qualified/converted leads) ─
            $deal = null;
            if (in_array($leadStatus, ['qualified', 'converted'])) {
                $dealCreatedAt = $createdAt->copy()->addDays(rand(1, 14));
                $dealValue = $this->getRandomDealValue();
                $dealStatus = $this->getDealStatusForLead($leadStatus);
                $stageId = $this->getStageIdForDealStatus($dealStatus, $pipelineStages);
                $closedAt = null;
                $lostReasonId = null;
                $lostNotes = null;

                if ($dealStatus === 'won') {
                    $closedAt = $dealCreatedAt->copy()->addDays(rand(3, 30));
                } elseif ($dealStatus === 'lost') {
                    $closedAt = $dealCreatedAt->copy()->addDays(rand(5, 45));
                    $lostReasonId = $lostReasons[array_rand($lostReasons)];
                    $lostNotes = 'Pelanggan menolak setelah konsultasi.';
                }

                $treatment = $this->beautyTreatments[array_rand($this->beautyTreatments)];

                $deal = Deal::create([
                    'lead_id' => $lead->id,
                    'name' => $treatment . ' - ' . $fullName,
                    'value' => $dealValue,
                    'pipeline_stage_id' => $stageId,
                    'status' => $dealStatus,
                    'lost_reason_id' => $lostReasonId,
                    'lost_notes' => $lostNotes,
                    'expected_close_date' => $dealCreatedAt->copy()->addDays(rand(7, 60)),
                    'closed_at' => $closedAt,
                    'assigned_to' => $salesUser->id,
                    'created_by' => $salesUser->id,
                    'created_at' => $dealCreatedAt,
                    'updated_at' => $closedAt ?? $dealCreatedAt->copy()->addHours(rand(1, 72)),
                ]);
            }

            // ─── Create Customer (for converted leads with won deals) ─
            if ($leadStatus === 'converted' && $deal && $deal->status === 'won') {
                $customerCreatedAt = $deal->closed_at ?? $createdAt->copy()->addDays(rand(15, 30));

                Customer::create([
                    'lead_id' => $lead->id,
                    'user_id' => $csUser->id,
                    'name' => $fullName,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'status' => rand(1, 10) > 1 ? 'active' : 'inactive',
                    'tags' => $this->tags[array_rand($this->tags)],
                    'notes' => 'Customer dari lead ' . $lead->id . '. Treatment: ' . ($deal->name ?? '-'),
                    'created_at' => $customerCreatedAt,
                    'updated_at' => $customerCreatedAt,
                ]);
                $customerCount++;
            }

            // ─── Create Activities ───────────────────────
            $numActivities = rand(1, 4);
            for ($a = 0; $a < $numActivities; $a++) {
                $actType = $this->activityTypes[array_rand($this->activityTypes)];
                $subjects = $this->activitySubjects[$actType];
                $actDate = $createdAt->copy()->addDays(rand(0, 7))->addHours(rand(8, 18));

                Activity::create([
                    'user_id' => $salesUser->id,
                    'activitable_type' => $deal ? Deal::class : Lead::class,
                    'activitable_id' => $deal ? $deal->id : $lead->id,
                    'type' => $actType,
                    'subject' => $subjects[array_rand($subjects)],
                    'description' => 'Aktivitas terkait ' . $fullName,
                    'duration' => rand(5, 60),
                    'result' => ['positive', 'neutral', 'negative'][array_rand(['positive', 'neutral', 'negative'])],
                    'activity_date' => $actDate,
                    'created_at' => $actDate,
                    'updated_at' => $actDate,
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("🎉 Done! Created:");
        $this->command->info("   📋 {$totalRecords} leads");
        $this->command->info("   💼 " . Deal::count() . " deals (total in DB)");
        $this->command->info("   👥 {$customerCount} customers");
        $this->command->info("   📊 " . Activity::count() . " activities (total in DB)");
    }

    // ─── Helper: Create Sales Users ──────────────────────

    private function createSalesUsers(): array
    {
        $salesNames = [
            ['name' => 'Anita Sari', 'email' => 'anita.sales@beautycrm.com'],
            ['name' => 'Budi Prasetyo', 'email' => 'budi.sales@beautycrm.com'],
            ['name' => 'Citra Dewi', 'email' => 'citra.sales@beautycrm.com'],
            ['name' => 'Denny Wijaya', 'email' => 'denny.sales@beautycrm.com'],
            ['name' => 'Eka Fitriani', 'email' => 'eka.sales@beautycrm.com'],
        ];

        $users = [];
        foreach ($salesNames as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'phone' => '0812345' . str_pad($i + 10, 4, '0', STR_PAD_LEFT),
                    'is_active' => true,
                ]
            );
            if (!$user->hasRole('Sales')) {
                $user->assignRole('Sales');
            }
            $users[] = $user;
        }

        // Also include existing sales user
        $existingSales = User::where('email', 'sales@beautycrm.com')->first();
        if ($existingSales) {
            $users[] = $existingSales;
        }

        return $users;
    }

    // ─── Helper: Create CS Users ─────────────────────────

    private function createCSUsers(): array
    {
        $csNames = [
            ['name' => 'Hani Permata', 'email' => 'hani.cs@beautycrm.com'],
            ['name' => 'Irma Kusuma', 'email' => 'irma.cs@beautycrm.com'],
        ];

        $users = [];
        foreach ($csNames as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'phone' => '0812345' . str_pad($i + 20, 4, '0', STR_PAD_LEFT),
                    'is_active' => true,
                ]
            );
            if (!$user->hasRole('Customer Service')) {
                $user->assignRole('Customer Service');
            }
            $users[] = $user;
        }

        // Also include existing CS user
        $existingCS = User::where('email', 'cs@beautycrm.com')->first();
        if ($existingCS) {
            $users[] = $existingCS;
        }

        return $users;
    }

    // ─── Helper: Generate dates spread across all periods ─

    private function generateDateDistribution(Carbon $now, int $total): array
    {
        $dates = [];

        // Today: ~15 records
        for ($i = 0; $i < 15; $i++) {
            $dates[] = $now->copy()->startOfDay()->addHours(rand(7, 20))->addMinutes(rand(0, 59));
        }

        // This week (excluding today): ~20 records
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 20; $i++) {
            $day = $startOfWeek->copy()->addDays(rand(0, $now->dayOfWeek - 1));
            if ($day->isToday()) {
                $day = $startOfWeek->copy(); // Push to start of week
            }
            $dates[] = $day->addHours(rand(7, 20))->addMinutes(rand(0, 59));
        }

        // This month (excluding this week): ~40 records
        $startOfMonth = $now->copy()->startOfMonth();
        for ($i = 0; $i < 40; $i++) {
            $maxDay = max(1, $now->day - 7);
            $day = $startOfMonth->copy()->addDays(rand(0, $maxDay - 1));
            $dates[] = $day->addHours(rand(7, 20))->addMinutes(rand(0, 59));
        }

        // Past 12 months (bulk of data): remaining records
        $remaining = $total - count($dates);
        for ($i = 0; $i < $remaining; $i++) {
            // Spread across 1-12 months ago with slight bias toward recent months
            $monthsAgo = $this->weightedRandom(1, 12);
            $date = $now->copy()->subMonths($monthsAgo);
            $daysInMonth = $date->daysInMonth;
            $date->day(rand(1, $daysInMonth));
            $dates[] = $date->addHours(rand(7, 20))->addMinutes(rand(0, 59));
        }

        // Shuffle to mix dates
        shuffle($dates);

        return $dates;
    }

    // Weighted random - bias toward lower values (more recent months)
    private function weightedRandom(int $min, int $max): int
    {
        $weights = [];
        for ($i = $min; $i <= $max; $i++) {
            $weights[$i] = max(1, $max - $i + 1) * 2; // More weight for recent months
        }

        $totalWeight = array_sum($weights);
        $rand = rand(1, $totalWeight);
        $cumulative = 0;

        foreach ($weights as $value => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $value;
            }
        }

        return $min;
    }

    private function getRandomLeadStatus(): string
    {
        $statuses = [
            'new' => 10,
            'contacted' => 15,
            'qualified' => 25,
            'converted' => 40,
            'closed' => 10,
        ];

        return $this->weightedSelect($statuses);
    }

    private function getQualificationForStatus(string $status): ?string
    {
        return match ($status) {
            'qualified', 'converted' => 'qualified',
            'closed' => ['unqualified', 'not_fit'][array_rand(['unqualified', 'not_fit'])],
            default => null,
        };
    }

    private function getDealStatusForLead(string $leadStatus): string
    {
        if ($leadStatus === 'converted') {
            // Most converted leads should have won deals
            return $this->weightedSelect(['won' => 65, 'lost' => 20, 'open' => 15]);
        }

        return $this->weightedSelect(['open' => 50, 'won' => 20, 'lost' => 30]);
    }

    private function getStageIdForDealStatus(string $status, $stages): int
    {
        if ($status === 'won' || $status === 'lost') {
            // Closing stage
            return $stages->last()->id;
        }

        // Random stage for open deals
        return $stages->random()->id;
    }

    private function getRandomDealValue(): float
    {
        // Beauty clinic treatment values in IDR
        $ranges = [
            [500000, 1500000],      // Basic treatments: 500K - 1.5M
            [1500000, 5000000],     // Medium treatments: 1.5M - 5M
            [5000000, 15000000],    // Premium treatments: 5M - 15M
            [15000000, 50000000],   // Package deals: 15M - 50M
            [50000000, 100000000],  // VIP packages: 50M - 100M
        ];

        $weights = [30, 35, 20, 10, 5]; // More basic/medium treatments
        $rangeIndex = $this->weightedSelectIndex($weights);
        $range = $ranges[$rangeIndex];

        // Round to nearest 100K
        return round(rand($range[0], $range[1]) / 100000) * 100000;
    }

    private function generateLeadNotes(): string
    {
        $notes = [
            'Tertarik dengan treatment facial untuk kulit sensitif.',
            'Ingin konsultasi masalah jerawat dan bekas jerawat.',
            'Mencari paket perawatan anti-aging.',
            'Referensi dari teman yang sudah treatment.',
            'Melihat promo di Instagram.',
            'Ingin perawatan rutin bulanan.',
            'Konsultasi untuk wedding preparation.',
            'Tertarik laser treatment untuk flek hitam.',
            'Mencari treatment body slimming.',
            'Ingin treatment PRP untuk rambut rontok.',
            'Tertarik paket membership premium.',
            'Follow up dari event beauty fair.',
            'Konsultasi skin booster dan vitamin drip.',
            'Tertarik treatment V-shape face.',
            'Ingin treatment threadlift non-surgical.',
        ];

        return $notes[array_rand($notes)];
    }

    private function weightedSelect(array $options): string
    {
        $totalWeight = array_sum($options);
        $rand = rand(1, $totalWeight);
        $cumulative = 0;

        foreach ($options as $value => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return (string) $value;
            }
        }

        return (string) array_key_first($options);
    }

    private function weightedSelectIndex(array $weights): int
    {
        $totalWeight = array_sum($weights);
        $rand = rand(1, $totalWeight);
        $cumulative = 0;

        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $index;
            }
        }

        return 0;
    }
}
