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
        $totalDeals = $this->applyPeriodFilter(Deal::query(), 'created_at', $period, $startDate, $endDate)->count();
        $totalRevenue = $this->applyPeriodFilter(Deal::won(), 'closed_at', $period, $startDate, $endDate)->sum('value');

        // Top Product
        $topProductResult = $this->applyPeriodFilter(Deal::won(), 'closed_at', $period, $startDate, $endDate)
            ->whereNotNull('product_name')
            ->select('product_name', DB::raw('count(*) as count'))
            ->groupBy('product_name')
            ->orderByDesc('count')
            ->first();
        $topProduct = $topProductResult ? $topProductResult->product_name : '-';

        // Target Achievement
        $multiplier = $this->getTargetMultiplier($period, $startDate, $endDate);
        
        $salesUsers = User::role('Sales')->where('is_active', true)->get();
        $totalRevenueTarget = $salesUsers->sum('revenue_target') * $multiplier;
        $activeSalesCount = $salesUsers->count();
        
        $targetAchievementRaw = $totalRevenueTarget > 0 ? round(($totalRevenue / $totalRevenueTarget) * 100, 1) : ($totalRevenue > 0 ? 100 : 0);
        $targetAchievement = min(100, $targetAchievementRaw);

        $revenueTrend = $this->getRevenueTrend(12);
        $funnel = $this->getFunnelData($period, $startDate, $endDate);
        $salesComparison = $this->getSalesComparison($period, $startDate, $endDate, $multiplier);
        $leadSourcesMonthly = $this->getLeadSourcesMonthly(6);
        $leaderboard = $this->getTeamLeaderboard($period, $startDate, $endDate, $multiplier);

        return compact(
            'totalDeals',
            'totalRevenue',
            'topProduct',
            'targetAchievement',
            'targetAchievementRaw',
            'totalRevenueTarget',
            'activeSalesCount',
            'revenueTrend',
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
        // 1. Mengambil semua "Kolom Status" (PipelineStage)
        $stages = PipelineStage::ordered()->get();
        $averageDealValue = Deal::won()->avg('value') ?? 0;

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
                'total_value' => $deals->count() * $averageDealValue, // Potensi nilai total
                'projected_revenue' => $deals->count() * $averageDealValue * ($stage->probability / 100), // Potensi disesuaikan peluang
            ];
        }

        return $pipeline;
    }

    // ─── Sales Performance Report ─────────────────────

    /**
     * Dipanggil oleh: Manager\ReportController@salesPerformance (Laporan Performa Sales)
     * Penjelasan: Menghitung metrik performa masing-masing user Sales (leads, deals, won, lost, win rate %, rata-rata hari closing, & jumlah aktivitas).
     */
    public function getSalesPerformance(?int $filterYear = null, ?int $filterMonth = null): Collection
    {
        $salesUsers = User::role('Sales')->where('is_active', true)->get();

        return $salesUsers->map(function ($user) use ($filterYear, $filterMonth) {

            $leads = Lead::where('assigned_to', $user->id);
            if ($filterYear) $leads->whereYear('created_at', $filterYear);
            if ($filterMonth) $leads->whereMonth('created_at', $filterMonth);

            $totalLeads = (clone $leads)->count();
            $qualifiedLeads = (clone $leads)->where('qualification', 'qualified')->count();

            $deals = Deal::where('assigned_to', $user->id);
            if ($filterYear) $deals->whereYear('created_at', $filterYear);
            if ($filterMonth) $deals->whereMonth('created_at', $filterMonth);
            
            $totalDeals = (clone $deals)->count();

            $wonDealsQuery = Deal::where('assigned_to', $user->id)->won();
            if ($filterYear) $wonDealsQuery->whereYear('closed_at', $filterYear);
            if ($filterMonth) $wonDealsQuery->whereMonth('closed_at', $filterMonth);

            $lostDealsQuery = Deal::where('assigned_to', $user->id)->lost();
            if ($filterYear) $lostDealsQuery->whereYear('closed_at', $filterYear);
            if ($filterMonth) $lostDealsQuery->whereMonth('closed_at', $filterMonth);

            $wonDeals = (clone $wonDealsQuery)->count();
            $lostDeals = (clone $lostDealsQuery)->count();

            $target = $user->monthly_target ?? 20;
            $revenueTarget = $user->revenue_target ?? 0;
            
            // Jika memilih 1 tahun penuh tanpa spesifik bulan, kalikan target dengan 12
            if ($filterYear && !$filterMonth) {
                $target *= 12;
                $revenueTarget *= 12;
            }

            $revenueAchieved = (clone $wonDealsQuery)->sum('value');

            $winRate = ($wonDeals + $lostDeals) > 0
                ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1)
                : 0;

            $activitiesQuery = Activity::where('user_id', $user->id);
            if ($filterYear) $activitiesQuery->whereYear('created_at', $filterYear);
            if ($filterMonth) $activitiesQuery->whereMonth('created_at', $filterMonth);
            $activities = $activitiesQuery->count();

            $wonDealsList = (clone $wonDealsQuery)->get();

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
                'revenue_achieved' => (float) $revenueAchieved,
                'revenue_target' => (float) $revenueTarget,
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
    public function getTeamLeaderboard(string $period = 'all', ?string $startDate = null, ?string $endDate = null, float $multiplier = 1): Collection
    {
        // 1. Ambil semua akun staf yang jabatannya Sales (aktif)
        $salesUsers = User::role('Sales')->where('is_active', true)->get();
        // 2. Lakukan perulangan (map) untuk setiap Sales
        return $salesUsers->map(function ($user) use ($period, $startDate, $endDate, $multiplier) {
            // Menghitung jumlah pendapatan (revenue) milik Sales berdasarkan periode yang dipilih.
            $revenueAchieved = $this->applyPeriodFilter(
                Deal::where('assigned_to', $user->id)->won(),
                'closed_at',
                $period,
                $startDate,
                $endDate
            )->sum('value');

            // Mengambil target pendapatan bulanan Sales, lalu disesuaikan dengan periode.
            $revenueTarget = ($user->revenue_target ?? 0) * $multiplier;

            // Menghitung persentase pencapaian
            $progressPercentage = $revenueTarget > 0 ? round(($revenueAchieved / $revenueTarget) * 100, 1) : ($revenueAchieved > 0 ? 100 : 0);
            
            // siapkan data untuk dikirim ke view
            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->avatar_url,
                'revenue_achieved' => $revenueAchieved,
                'revenue_target' => $revenueTarget,
                'progress_percentage' => $progressPercentage,
            ];
        // sortByDesc('revenue_achieved') bertugas mengurutkan array data dari pendapatan tertinggi ke terendah.
        })->sortByDesc('revenue_achieved')->values();
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

        // Mengambil target bulanan Sales (Revenue).
        $target = $user->revenue_target ?? 0;
        $revenueAchieved = (clone $deals)->won()->sum('value');

        // Menghitung persentase keberhasilan Sales.
        $winRate = ($wonDeals + $lostDeals) > 0
            ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1)
            : 0;

        // Menghitung jumlah aktivitas yang dilakukan Sales.
        $activities = Activity::where('user_id', $userId)->count();

        // Menyiapkan data pendapatan WON setiap bulan selama 6 bulan terakhir.
        $monthlyRevenue = [];
        $now = Carbon::now();

        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);

            $revenue = Deal::where('assigned_to', $userId)
                ->won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->sum('value');

            $monthlyRevenue[] = [
                'month' => $date->format('M'),
                'revenue' => (float) $revenue,
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
            'revenue_achieved' => $revenueAchieved,
            'win_rate' => $winRate,
            'activities' => $activities,
            'monthly_revenue' => $monthlyRevenue,
            'recent_deals' => $recentDeals,
        ];
    }

    // ─── Forecast ─────────────────────────────────────
    // Function untuk meramal pendapatan berdasarkan data yang masuk
    public function getForecastData(?int $filterYear = null, ?int $filterMonth = null): array
    {
        // Mengambil data Deal WON dan Deal OPEN untuk memperkirakan hasil penjualan.
        $now = Carbon::now();
        $months = [];

        // Karena deal belum diisi harganya sampai status WON, kita hitung Rata-Rata nilai Deal yang sudah WON
        $averageDealValue = Deal::won()->avg('value') ?? 0;

        // Menghitung Rata-rata Kecepatan Closing (Hari) dari data historis
        $wonDealsData = Deal::won()->whereNotNull('closed_at')->get(['created_at', 'closed_at']);
        $averageDaysToClose = $wonDealsData->count() > 0 
            ? round($wonDealsData->avg(fn($d) => $d->created_at->diffInDays($d->closed_at))) 
            : 14; // Default 14 hari jika belum ada data historis

        // Menyiapkan daftar bulan yang akan ditampilkan
        $dates = [];
        if ($filterYear && $filterMonth) {
            // Hanya 1 bulan spesifik
            $dates[] = Carbon::createFromDate($filterYear, $filterMonth, 1);
        } elseif ($filterYear) {
            // Seluruh 12 bulan di tahun yang dipilih
            for ($m = 1; $m <= 12; $m++) {
                $dates[] = Carbon::createFromDate($filterYear, $m, 1);
            }
        } else {
            // Default: Januari sampai Desember di tahun ini (current year)
            $currentYear = $now->year;
            for ($m = 1; $m <= 12; $m++) {
                $dates[] = Carbon::createFromDate($currentYear, $m, 1);
            }
        }

        // Ambil semua Deal OPEN untuk diproyeksikan
        $openDeals = Deal::open()->with('pipelineStage')->get();

        foreach ($dates as $date) {
            $label = $date->format('M Y');
            $isPast = $date->lt($now->copy()->startOfMonth());

            // --- 1. DATA AKTUAL (Masa Lalu) ---
            // Hitung JUMLAH PENDAPATAN (value) yang SUDAH BERHASIL (won) pada bulan tersebut.
            $actual = Deal::won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->sum('value');

            // --- 2. DATA PROYEKSI (Masa Depan) ---
            // Hitung prediksi berdasarkan 'Kecerdasan Data' (Rata-rata Waktu Closing historis)
            $projected = $openDeals->filter(function($deal) use ($date, $averageDaysToClose, $now) {
                // Prediksi: Kapan deal ini selesai? (Tanggal dibuat + rata-rata historis hari closing)
                $predictedDate = $deal->created_at->copy()->addDays($averageDaysToClose);
                
                // Jika ternyata prediksinya jatuh di masa lalu (dealnya molor), 
                // kita tarik jadwalnya ke bulan ini (sekarang) agar uangnya tidak hilang ke masa lalu.
                if ($predictedDate->lt($now->copy()->startOfMonth())) {
                    $predictedDate = $now->copy(); 
                }

                return $predictedDate->year === $date->year && $predictedDate->month === $date->month;
            })->sum(function($deal) use ($averageDealValue) {
                $probability = $deal->pipelineStage ? ($deal->pipelineStage->probability / 100) : 0;
                return $probability * $averageDealValue;
            });

            $months[] = [
                'month' => $label,
                'month_short' => $date->format('M'),
                'actual' => (float) $actual,
                'projected' => (float) $projected,
                'is_past' => $isPast,
            ];
        }

        $totalProjected = collect($months)->where('is_past', false)->sum('projected');
        $totalActual = collect($months)->where('is_past', true)->sum('actual');

        // Menghitung perkiraan nilai pendapatan maksimal jika semua Deal OPEN berhasil.
        $openDealsCount = Deal::open()->count();
        $bestCase = $openDealsCount * $averageDealValue;
        
        // Mencari stage Closing untuk menghitung kemungkinan pendapatan terburuk (hanya yang sudah sangat dekat closing).
        $closingStageId = PipelineStage::where('name', 'like', '%Closing%')->value('id');
        
        $worstCaseCount = $closingStageId ? Deal::open()->where('pipeline_stage_id', $closingStageId)->count() : 0;
        $worstCase = $worstCaseCount * $averageDealValue;

        return [
            'months' => $months,
            'total_projected' => $totalProjected,
            'total_actual' => $totalActual,
            'best_case' => $bestCase,
            'worst_case' => $worstCase,
        ];
    }

    // ─── Revenue Target Data ───────────────────────────
    public function getRevenueTargetData(): array
    {
        $now = Carbon::now();
        $salesUsers = User::role('Sales')->where('is_active', true)->get();

        // 1. Hitung total revenue bulan ini (dari Deal WON)
        $totalRevenueThisMonth = Deal::won()
            ->whereYear('closed_at', $now->year)
            ->whereMonth('closed_at', $now->month)
            ->sum('value');

        // 2. Kinerja per Sales
        $salesPerformance = $salesUsers->map(function ($user) use ($now) {
            $wonDealsThisMonth = Deal::where('assigned_to', $user->id)
                ->won()
                ->whereYear('closed_at', $now->year)
                ->whereMonth('closed_at', $now->month);

            $revenueAchieved = $wonDealsThisMonth->sum('value');
            $membersWonThisMonth = $wonDealsThisMonth->count();

            $totalMembersWon = Deal::where('assigned_to', $user->id)->won()->count();

            $target = $user->revenue_target ?? 0;
            $progress = $target > 0 ? round(($revenueAchieved / $target) * 100, 1) : ($revenueAchieved > 0 ? 100 : 0);

            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->avatar_url,
                'revenue_target' => $target,
                'revenue_achieved' => $revenueAchieved,
                'progress_percentage' => $progress > 100 ? 100 : $progress, // Cap at 100 for visual progress bar
                'progress_raw' => $progress, // Actual percentage
                'members_won_this_month' => $membersWonThisMonth,
                'total_members_won' => $totalMembersWon,
            ];
        })->sortByDesc('revenue_achieved')->values();

        // 3. Pendapatan setiap bulan (6 bulan terakhir)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $revenue = Deal::won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->sum('value');

            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
                'members' => Deal::won()
                    ->whereYear('closed_at', $date->year)
                    ->whereMonth('closed_at', $date->month)
                    ->count(),
            ];
        }

        return [
            'total_revenue_this_month' => $totalRevenueThisMonth,
            'sales_performance' => $salesPerformance,
            'monthly_revenue' => $monthlyRevenue,
        ];
    }

    // ─── Lost Reasons ──────────────────────────────────

    public function getLostReasons(): array
    {
        $reasons = \App\Models\LostReason::all();
        $totalLostDeals = Deal::lost()->count();
        $totalLostRevenue = Deal::lost()->sum('value');

        $data = [];
        foreach ($reasons as $reason) {
            $count = Deal::lost()->where('lost_reason_id', $reason->id)->count();
            $potentialRevenue = Deal::lost()->where('lost_reason_id', $reason->id)->sum('value');
            $percentage = $totalLostDeals > 0 ? round(($count / $totalLostDeals) * 100, 1) : 0;
            
            $data[] = [
                'name' => $reason->name,
                'count' => $count,
                'potential_revenue' => (float) $potentialRevenue,
                'percentage' => $percentage,
            ];
        }

        return [
            'total' => $totalLostDeals,
            'total_lost_revenue' => $totalLostRevenue,
            'reasons' => collect($data)->sortByDesc('potential_revenue')->values()->all(),
        ];
    }

    // ─── Lead Sources ──────────────────────────────────

    public function getLeadSources(): array
    {
        $sources = \App\Models\LeadSource::all();
        $colors = ['#F59E0B', '#3B82F6', '#10B981', '#EF4444', '#8B5CF6', '#EC4899', '#6B7280'];
        
        $data = [];
        foreach ($sources as $idx => $source) {
            $leadsCount = \App\Models\Lead::where('lead_source_id', $source->id)->count();
            
            $revenue = Deal::won()->whereHas('lead', function($q) use ($source) {
                $q->where('lead_source_id', $source->id);
            })->sum('value');

            $data[] = [
                'name' => $source->name,
                'count' => $leadsCount,
                'revenue' => (float) $revenue,
                'color' => $source->color ?? ($colors[$idx % count($colors)]),
            ];
        }

        return collect($data)->sortByDesc('revenue')->values()->all();
    }

    // ─── Audit Logs ───────────────────────────────────

    public function getAuditLogs(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        return AuditLog::with('user')
            ->filterAction($filters['action'] ?? null)
            ->filterUser($filters['user_id'] ?? null)
            ->filterModule($filters['module'] ?? null)
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    // ─── Revenue Report (Trend) ───────────────────────
    public function getRevenueReport(int $months = 12, ?int $filterYear = null, ?int $filterMonth = null): array
    {
        $monthly = [];
        $now = Carbon::now();
        $totalRevenue = 0;

        $dates = [];
        if ($filterYear && $filterMonth) {
            $dates[] = Carbon::createFromDate($filterYear, $filterMonth, 1);
        } elseif ($filterYear) {
            for ($m = 1; $m <= 12; $m++) {
                $dates[] = Carbon::createFromDate($filterYear, $m, 1);
            }
        } else {
            for ($i = $months - 1; $i >= 0; $i--) {
                $dates[] = $now->copy()->subMonths($i);
            }
        }

        foreach ($dates as $date) {
            $revenue = Deal::won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->sum('value');

            $totalRevenue += $revenue;

            $monthly[] = [
                'month' => $date->format('M Y'),
                'month_short' => $date->format('M'),
                'revenue' => (float) $revenue,
            ];
        }

        return [
            'total' => $totalRevenue,
            'average' => count($dates) > 0 ? $totalRevenue / count($dates) : 0,
            'monthly' => $monthly,
        ];
    }

    // ─── Private Helpers ──────────────────────────────

    private function getRevenueTrend(int $months): array
    {
        $data = [];
        $now = Carbon::now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $revenue = Deal::won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->sum('value');

            $data[] = [
                'month' => $date->format('M'),
                'revenue' => $revenue,
            ];
        }

        return $data;
    }
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

    private function getSalesComparison(string $period = 'all', ?string $startDate = null, ?string $endDate = null, float $multiplier = 1): Collection
    {
        // Mengambil semua Sales aktif untuk dibandingkan performanya.
        $salesUsers = User::role('Sales')->where('is_active', true)->get();

        return $salesUsers->map(fn($user) => [
            'name' => $user->name,
            'target_revenue' => ($user->revenue_target ?? 0) * $multiplier,
            'achieved_revenue' => $this->applyPeriodFilter(
                Deal::where('assigned_to', $user->id)->won(),
                'closed_at',
                $period,
                $startDate,
                $endDate
            )->sum('value'),
        ])->sortByDesc('achieved_revenue')->values();
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

        if ($period === 'month_year' && request('filter_month') && request('filter_year')) {
            return $query->whereYear($column, request('filter_year'))->whereMonth($column, request('filter_month'));
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

    private function getTargetMultiplier(string $period, ?string $startDate, ?string $endDate): float
    {
        $now = Carbon::now();

        if ($period === 'today') {
            return 1 / $now->daysInMonth;
        }

        if ($period === 'this_week') {
            // Target untuk satu minggu (7 hari) proporsional dengan bulan ini
            return 7 / $now->daysInMonth;
        }

        if ($period === 'this_month' || $period === 'last_month') {
            return 1;
        }

        if ($period === 'month_year' && request('filter_month') && request('filter_year')) {
            return 1;
        }

        if ($period === 'this_year' || $period === 'last_year') {
            return 12;
        }

        if ($period === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->startOfDay(); // Gunakan startOfDay juga untuk hitung selisih hari
            
            // Jika dalam bulan dan tahun yang sama
            if ($start->format('Y-m') === $end->format('Y-m')) {
                $days = $start->diffInDays($end) + 1; // +1 untuk menghitung hari ini juga
                return $days / $start->daysInMonth;
            }

            // Jika lintas bulan
            $multiplier = 0;
            $currentDate = $start->copy();
            
            while ($currentDate->format('Y-m') <= $end->format('Y-m')) {
                if ($currentDate->format('Y-m') === $start->format('Y-m')) {
                    // Bulan pertama: sisa hari di bulan tersebut
                    $daysInThisMonth = $start->daysInMonth - $start->day + 1;
                    $multiplier += $daysInThisMonth / $start->daysInMonth;
                } elseif ($currentDate->format('Y-m') === $end->format('Y-m')) {
                    // Bulan terakhir: hari yang dilalui di bulan tersebut
                    $daysInThisMonth = $end->day;
                    $multiplier += $daysInThisMonth / $end->daysInMonth;
                } else {
                    // Bulan penuh di tengah
                    $multiplier += 1;
                }
                $currentDate->addMonth();
            }
            return $multiplier;
        }

        if ($period === 'all') {
            $firstDeal = Deal::orderBy('created_at')->first();
            if ($firstDeal) {
                $months = ceil($firstDeal->created_at->floatDiffInMonths(Carbon::now()));
                return max(1, $months);
            }
            return 1;
        }

        return 1;
    }
}