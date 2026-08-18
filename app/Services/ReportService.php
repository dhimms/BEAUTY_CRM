<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\AuditLog;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LostReason;
use App\Models\PipelineStage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    // ─── Manager Dashboard ────────────────────────────
    /**
     * Dipanggil oleh: Manager\DashboardController@index (Dashboard Manager)
     * Penjelasan: Mengumpulkan statistik utama (total lead, deal, won, lost, win rate, funnel, perbandingan performa sales, & leaderboard).
     * Fokus: Menghitung Metrik Agregat
     */
    public function getManagerDashboard(string $period = 'all', ?string $startDate = null, ?string $endDate = null): array
    {
        // 1. menghitung jumlah total leads, deals, won, lost
        $totalLeads = $this->applyPeriodFilter(Lead::query(), 'created_at', $period, $startDate, $endDate)->count();
        $totalDeals = $this->applyPeriodFilter(Deal::query(), 'created_at', $period, $startDate, $endDate)->count();
        $wonDeals = $this->applyPeriodFilter(Deal::won(), 'closed_at', $period, $startDate, $endDate)->count();
        $lostDeals = $this->applyPeriodFilter(Deal::lost(), 'closed_at', $period, $startDate, $endDate)->count();

        $winRate = ($wonDeals + $lostDeals) > 0
            ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1)
            : 0;

        $memberTrend = $this->getMemberTrend(12);
        $funnel = $this->getFunnelData($period, $startDate, $endDate);
        $salesComparison = $this->getSalesComparison($period, $startDate, $endDate);
        $leadSourcesMonthly = $this->getLeadSourcesMonthly(6);
        $leaderboard = $this->getTeamLeaderboard($period, $startDate, $endDate);

        return compact(
            'totalLeads',
            'totalDeals',
            'wonDeals',
            'winRate',
            'memberTrend',
            'funnel',
            'salesComparison',
            'leadSourcesMonthly',
            'leaderboard'
        );
    }

    // ─── Pipeline ─────────────────────────────────────
    /**
     * Dipanggil oleh: Manager\ReportController@pipeline (Laporan Pipeline Manager)
     * Penjelasan: Mengambil seluruh data deal open yang dikelompokkan berdasarkan stage pipeline.
     * Fitur: Kanban board
     */
    public function getPipelineData(): array
    {
        // 1. Mengambil semua "Kolom Status" (PipelineStage
        $stages = PipelineStage::ordered()->get();

        $pipeline = [];
        // 2. Lakukan perulangan untuk mengecek isi setiap kolom
        foreach ($stages as $stage) {
            // 3. Ambil Deal (transaksi) yang nyangkut di kolom status ini
            $deals = Deal::with(['lead', 'assignedUser'])
                ->where('pipeline_stage_id', $stage->id)
                ->where('status', 'open') // Hanya yang masih berstatus terbuka/negosiasi
                ->orderBy('expected_close_date')
                ->get()

                // 4. Ubah data transaksi agar rapi untuk tampilan web
                ->map(fn($deal) => [
                    'id' => $deal->id,
                    'name' => $deal->name,
                    'lead_name' => $deal->lead?->name ?? '-',
                    'assigned_to' => $deal->assignedUser?->name ?? '-',
                    'expected_close' => $deal->expected_close_date?->format('d M Y'),
                    'status' => $deal->status,
                ]);
                // 5. masukan ke array
                $pipeline[] = [
                'id' => $stage->id,
                'name' => $stage->name,
                'color' => $stage->color,
                'probability' => $stage->probability, // Peluang menang (Misal: 'Closing' probabilitas 90%)
                'deals' => $deals, // Daftarnya
                'count' => $deals->count(), // Total ada berapa orang di kolom ini
            ];
        }

        return $pipeline;
    }

    // ─── Sales Performance Report ─────────────────────

    /**
     * Dipanggil oleh: Manager\ReportController@salesPerformance (Laporan Performa Sales)
     * Penjelasan: Menghitung metrik performa masing-masing user Sales (leads, deals, won, lost, win rate %, rata-rata hari closing, & jumlah aktivitas).
     */
    public function getSalesPerformance(): Collection
    {
        $salesUsers = User::role('Sales')->where('is_active', true)->get();

        return $salesUsers->map(function ($user) {

            $leads = Lead::where('assigned_to', $user->id);
            $totalLeads = $leads->count();
            $qualifiedLeads = (clone $leads)->where('qualification', 'qualified')->count();

            $deals = Deal::where('assigned_to', $user->id);
            $totalDeals = $deals->count();
            $wonDeals = (clone $deals)->won()->count();
            $lostDeals = (clone $deals)->lost()->count();

            $target = $user->monthly_target ?? 20;

            $winRate = ($wonDeals + $lostDeals) > 0
                ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1)
                : 0;

            $activities = Activity::where('user_id', $user->id)->count();

            $wonDealsList = (clone $deals)->won()->get();

            $totalDays = 0;

            foreach ($wonDealsList as $wd) {
                if ($wd->closed_at) {
                    $totalDays += $wd->created_at->diffInDays($wd->closed_at);
                }
            }

            $avgCloseTime = $wonDeals > 0 ? round($totalDays / $wonDeals, 1) : 0;

            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->avatar_url,
                'leads' => $totalLeads,
                'qualified' => $qualifiedLeads,
                'deals' => $totalDeals,
                'won' => $wonDeals,
                'lost' => $lostDeals,
                'win_rate' => $winRate,
                'target' => $target,
                'activities' => $activities,
                'avg_close_time' => $avgCloseTime,
            ];
        })->sortByDesc('won')->values();
    }

    // ─── Team Leaderboard ─────────────────────────────

    /**
     * Dipanggil oleh: Manager\ReportController@leaderboard (Leaderboard Tim Sales)
     * Penjelasan: Mengurutkan peringkat tim sales berdasarkan total Deal WON pada periode yang dipilih.
     */
    public function getTeamLeaderboard(string $period = 'all', ?string $startDate = null, ?string $endDate = null): Collection
    {
        // 1. Ambil semua akun staf yang jabatannya Sales (aktif)
        $salesUsers = User::role('Sales')->where('is_active', true)->get();
        // 2. Lakukan perulangan (map) untuk setiap Sales
        return $salesUsers->map(function ($user) use ($period, $startDate, $endDate) {
            // Menghitung jumlah Deal WON milik Sales berdasarkan periode yang dipilih.
            $wonDeals = $this->applyPeriodFilter(
                Deal::where('assigned_to', $user->id)->won(),
                'closed_at',
                $period,
                $startDate,
                $endDate
            )->count();

            // Menghitung jumlah Deal LOST milik Sales berdasarkan periode yang dipilih.
            $lostDeals = $this->applyPeriodFilter(
                Deal::where('assigned_to', $user->id)->lost(),
                'closed_at',
                $period,
                $startDate,
                $endDate
            )->count();

            // Mengambil target bulanan Sales. Kalau tidak ada, menggunakan target 20.
            $target = $user->monthly_target ?? 20;

            // Menghitung jumlah Lead yang dimiliki Sales berdasarkan periode.
            $leads = $this->applyPeriodFilter(
                Lead::where('assigned_to', $user->id),
                'created_at',
                $period,
                $startDate,
                $endDate
            )->count();

            // 3. LOGIKA MATEMATIKA (Win Rate)
            // Menghitung persentase keberhasilan Sales berdasarkan Deal WON dan LOST.
            $winRate = ($wonDeals + $lostDeals) > 0
                ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1)
                : 0;
            
            // 4. siapkan data untuk dikirim ke view
            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->avatar_url,
                'leads' => $leads,
                'won' => $wonDeals,
                'target' => $target,
                'win_rate' => $winRate,
            ];
        // sortByDesc('won') bertugas mengurutkan array data dari nilai kemenangan tertinggi ke terendah.
        })->sortByDesc('won')->values();
    }

    // ─── Team Member Detail ───────────────────────────

    public function getTeamMemberDetail(int $userId): array
    {
        // Mengambil data Sales berdasarkan ID user yang dipilih.
        $user = User::findOrFail($userId);

        // Mengambil semua Lead milik Sales tersebut.
        $leads = Lead::where('assigned_to', $userId);

        // Menghitung jumlah seluruh Lead.
        $totalLeads = $leads->count();

        // Menghitung jumlah Lead yang sudah qualified.
        $qualifiedLeads = (clone $leads)->where('qualification', 'qualified')->count();

        // Menghitung jumlah Lead yang sudah dikonversi menjadi Deal.
        $convertedLeads = (clone $leads)->where('status', 'converted')->count();

        // Mengambil semua Deal milik Sales tersebut.
        $deals = Deal::where('assigned_to', $userId);

        // Menghitung jumlah seluruh Deal.
        $totalDeals = $deals->count();

        // Menghitung jumlah Deal yang WON.
        $wonDeals = (clone $deals)->won()->count();

        // Menghitung jumlah Deal yang LOST.
        $lostDeals = (clone $deals)->lost()->count();

        // Menghitung jumlah Deal yang masih OPEN.
        $openDeals = (clone $deals)->open()->count();

        // Mengambil target bulanan Sales.
        $target = $user->monthly_target ?? 20;

        // Menghitung persentase keberhasilan Sales.
        $winRate = ($wonDeals + $lostDeals) > 0
            ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1)
            : 0;

        // Menghitung jumlah aktivitas yang dilakukan Sales.
        $activities = Activity::where('user_id', $userId)->count();

        // Menyiapkan data jumlah Deal WON setiap bulan selama 6 bulan terakhir.
        $monthlyWonCount = [];
        $now = Carbon::now();

        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);

            // Menghitung jumlah Deal WON Sales pada bulan tersebut.
            $count = Deal::where('assigned_to', $userId)
                ->won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->count();

            $monthlyWonCount[] = [
                'month' => $date->format('M'),
                'count' => $count,
            ];
        }

        // Mengambil 10 Deal terakhir milik Sales.
        $recentDeals = Deal::with(['pipelineStage', 'lead'])
            ->where('assigned_to', $userId)
            ->latest()
            ->take(10)
            ->get();

        return [
            'user' => $user,
            'leads' => $totalLeads,
            'qualified' => $qualifiedLeads,
            'converted' => $convertedLeads,
            'deals' => $totalDeals,
            'won' => $wonDeals,
            'lost' => $lostDeals,
            'open' => $openDeals,
            'target' => $target,
            'win_rate' => $winRate,
            'activities' => $activities,
            'monthly_won_count' => $monthlyWonCount,
            'recent_deals' => $recentDeals,
        ];
    }

    // ─── Forecast ─────────────────────────────────────
    // Function untuk meramal member baru berdasarkan data yang masuk
    public function getForecastData(): array
    {
        // Mengambil data Deal WON dan Deal OPEN untuk memperkirakan hasil penjualan.
        $now = Carbon::now();
        $months = [];

        // Looping 12 Bulan (5 bulan lalu, sekarang, 6 bulan ke depan)
        for ($i = 5; $i >= -6; $i--) {
            $date = $now->copy()->subMonths($i);
            $label = $date->format('M Y');
            $isPast = $date->lt($now->copy()->startOfMonth());

            // --- 1. DATA AKTUAL (Masa Lalu) ---
            // Hitung JUMLAH ORANG yang SUDAH BERHASIL (won) gabung pada bulan tersebut.
            $actual = Deal::won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->count();

            // --- 2. DATA PROYEKSI (Masa Depan) ---
            // Hitung prediksi member baru dari transaksi yg masih gantung (open) 
            // dan rencana closingnya adalah di bulan tersebut.
            $projected = Deal::open()
                ->with('pipelineStage')
                ->whereYear('expected_close_date', $date->year)
                ->whereMonth('expected_close_date', $date->month)
                ->get()
                ->sum(fn($deal) => $deal->weighted_value);

            $months[] = [
                'month' => $label,
                'month_short' => $date->format('M'),
                'actual' => (float) $actual,
                'projected' => round((float) $projected, 2),
                'is_past' => $isPast,
            ];
        }

        $totalProjected = collect($months)->where('is_past', false)->sum('projected');
        $totalActual = collect($months)->where('is_past', true)->sum('actual');

        // Menghitung perkiraan jumlah orang terbaik jika semua Deal OPEN berhasil.
        $bestCase = Deal::open()->count();
        
        // Mencari stage Closing untuk menghitung kemungkinan hasil terburuk.
        $closingStageId = PipelineStage::where('name', 'like', '%Closing%')->value('id');
        
        $worstCase = $closingStageId ? Deal::open()->where('pipeline_stage_id', $closingStageId)->count() : 0;

        return [
            'months' => $months,
            'total_projected' => $totalProjected,
            'total_actual' => $totalActual,
            'best_case' => $bestCase,
            'worst_case' => $worstCase,
        ];
    }

    // ─── Private Helpers ──────────────────────────────

    private function getFunnelData(string $period = 'all', ?string $startDate = null, ?string $endDate = null): array
    {
        // Membuat data alur Sales dari Lead sampai menjadi Deal WON.
        $totalLeads = $this->applyPeriodFilter(Lead::query(), 'created_at', $period, $startDate, $endDate)->count();

        // Menghitung jumlah Lead yang sudah qualified.
        $qualified = $this->applyPeriodFilter(
            Lead::where('qualification', 'qualified'),
            'created_at',
            $period,
            $startDate,
            $endDate
        )->count();

        // Menghitung jumlah Lead yang sudah menjadi Deal.
        $deals = $this->applyPeriodFilter(
            Deal::query(),
            'created_at',
            $period,
            $startDate,
            $endDate
        )->count();

        // Menghitung jumlah Deal yang berhasil WON.
        $won = $this->applyPeriodFilter(
            Deal::won(),
            'closed_at',
            $period,
            $startDate,
            $endDate
        )->count();

        return [
            ['label' => 'Leads', 'value' => $totalLeads, 'color' => '#3B82F6'],
            ['label' => 'Qualified', 'value' => $qualified, 'color' => '#8B5CF6'],
            ['label' => 'Deals', 'value' => $deals, 'color' => '#F59E0B'],
            ['label' => 'Won', 'value' => $won, 'color' => '#10B981'],
        ];
    }

    private function getSalesComparison(string $period = 'all', ?string $startDate = null, ?string $endDate = null): Collection
    {
        // Mengambil semua Sales aktif untuk dibandingkan performanya.
        $salesUsers = User::role('Sales')->where('is_active', true)->get();

        return $salesUsers->map(fn($user) => [

            // Menghitung jumlah Deal milik masing-masing Sales.
            'name' => $user->name,
            'deals' => $this->applyPeriodFilter(
                Deal::where('assigned_to', $user->id),
                'created_at',
                $period,
                $startDate,
                $endDate
            )->count(),

            // Menghitung jumlah Deal WON masing-masing Sales.
            'won' => $this->applyPeriodFilter(
                Deal::where('assigned_to', $user->id)->won(),
                'closed_at',
                $period,
                $startDate,
                $endDate
            )->count(),
        ])->sortByDesc('won')->values();
    }

    private function getLeadSourcesMonthly(int $months): array
    {
        // Mengambil sumber Lead yang masih aktif untuk melihat dari mana Lead berasal.
        $sources = LeadSource::where('is_active', true)->get();

        $now = Carbon::now();
        $labels = [];
        $datasets = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $labels[] = $date->format('M');
        }

        $colors = ['#F59E0B', '#3B82F6', '#10B981', '#EF4444', '#8B5CF6', '#EC4899', '#6B7280'];

        foreach ($sources as $idx => $source) {
            $data = [];

            for ($i = $months - 1; $i >= 0; $i--) {
                $date = $now->copy()->subMonths($i);

                // Menghitung jumlah Lead dari sumber tertentu pada bulan tertentu.
                $count = Lead::where('lead_source_id', $source->id)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $data[] = $count;
            }

            $datasets[] = [
                'label' => $source->name,
                'data' => $data,
                'color' => $source->color ?? ($colors[$idx % count($colors)]),
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    // 1. membuat fungsi applyPeriodFilter untuk membantu menyaring data berdasarkan periode waktu
    private function applyPeriodFilter($query, string $column, string $period, ?string $startDate = null, ?string $endDate = null)
    {
        // Fungsi ini digunakan untuk menyaring data berdasarkan periode waktu yang dipilih.
        if ($period === 'custom' && $startDate && $endDate) {
            return $query->whereBetween($column, [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        // 2.  menggunakan match untuk menentukan periode filter
        // Fungsi match seperti switch-case tapi lebih modern.
        return match ($period) {
            // Sistem menyisipkan perintah SQL: Tampilkan data yang tanggalnya HANYA sama dengan hari ini.
            'today' => $query->whereDate($column, Carbon::today()),

            // Mengambil data selama minggu ini.
            'this_week' => $query->whereBetween($column, [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]),

            // Mengambil data selama bulan ini.
            'this_month' => $query
                ->whereYear($column, Carbon::now()->year)
                ->whereMonth($column, Carbon::now()->month),

            // Mengambil data selama tahun ini.
            'this_year' => $query->whereYear($column, Carbon::now()->year),

            // Kalau tidak ada filter periode, semua data ditampilkan.
            default => $query,
        };
    }
}