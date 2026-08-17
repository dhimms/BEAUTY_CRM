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
    //  Menghitung Metrik Agregat
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
        // Member trend 12 months
        $memberTrend = $this->getMemberTrend(12);

        // Funnel data
        $funnel = $this->getFunnelData($period, $startDate, $endDate);

        // Sales performance comparison
        $salesComparison = $this->getSalesComparison($period, $startDate, $endDate);

        // Lead sources breakdown by month
        $leadSourcesMonthly = $this->getLeadSourcesMonthly(6);

        // Leaderboard
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
    // fitur kanban board
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

    // ─── Member Acquisition Report ───────────────────────────────

    public function getMemberAcquisitionReport(int $months = 12): array
    {
        $data = [];
        $now = Carbon::now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $dealsCount = Deal::won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->count();

            $data[] = [
                'month' => $date->format('M Y'),
                'month_short' => $date->format('M'),
                'deals_count' => $dealsCount,
            ];
        }

        $totalMembers = array_sum(array_column($data, 'deals_count'));
        $avgMonthly = $months > 0 ? $totalMembers / $months : 0;

        return [
            'monthly' => $data,
            'total' => $totalMembers,
            'average' => $avgMonthly,
        ];
    }

    // ─── Lost Reasons Report ──────────────────────────

    public function getLostReasons(): array
    {
        $totalLost = Deal::lost()->count();

        $reasons = Deal::lost()
            ->whereNotNull('lost_reason_id')
            ->join('lost_reasons', 'deals.lost_reason_id', '=', 'lost_reasons.id')
            ->selectRaw('lost_reasons.name, COUNT(*) as count')
            ->groupBy('lost_reasons.name')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'name' => $item->name,
                'count' => $item->count,
                'percentage' => $totalLost > 0 ? round(($item->count / $totalLost) * 100, 1) : 0,
            ]);

        $noReason = Deal::lost()->whereNull('lost_reason_id')->count();
        if ($noReason > 0) {
            $reasons->push([
                'name' => 'Tidak Disebutkan',
                'count' => $noReason,
                'percentage' => $totalLost > 0 ? round(($noReason / $totalLost) * 100, 1) : 0,
            ]);
        }

        return [
            'reasons' => $reasons,
            'total' => $totalLost,
        ];
    }

    // ─── Lead Sources Report ──────────────────────────

    public function getLeadSources(): Collection
    {
        return LeadSource::withCount('leads')
            ->orderByDesc('leads_count')
            ->get()
            ->map(fn($source) => [
                'name' => $source->name,
                'count' => $source->leads_count,
                'color' => $source->color ?? '#6B7280',
            ]);
    }

    // ─── Team Leaderboard ─────────────────────────────

    public function getTeamLeaderboard(string $period = 'all', ?string $startDate = null, ?string $endDate = null): Collection
    {
        // 1. Ambil semua akun staf yang jabatannya Sales 
        $salesUsers = User::role('Sales')->where('is_active', true)->get();
        // 2. Lakukan perulangan (map) untuk setiap Sales
        return $salesUsers->map(function ($user) use ($period, $startDate, $endDate) {
            // Hitung transaksi yang sukses dan gagal milik spesifik Sales ini ($user->id)
            $wonDeals = $this->applyPeriodFilter(Deal::where('assigned_to', $user->id)->won(), 'closed_at', $period, $startDate, $endDate)->count();
            $lostDeals = $this->applyPeriodFilter(Deal::where('assigned_to', $user->id)->lost(), 'closed_at', $period, $startDate, $endDate)->count();
            $target = $user->monthly_target ?? 20;
            $leads = $this->applyPeriodFilter(Lead::where('assigned_to', $user->id), 'created_at', $period, $startDate, $endDate)->count();
            // 3. LOGIKA MATEMATIKA (Win Rate)
            // Kemenangan dibagi Total Transaksi (Menang + Kalah)
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
        $user = User::findOrFail($userId);

        $leads = Lead::where('assigned_to', $userId);
        $totalLeads = $leads->count();
        $qualifiedLeads = (clone $leads)->where('qualification', 'qualified')->count();
        $convertedLeads = (clone $leads)->where('status', 'converted')->count();

        $deals = Deal::where('assigned_to', $userId);
        $totalDeals = $deals->count();
        $wonDeals = (clone $deals)->won()->count();
        $lostDeals = (clone $deals)->lost()->count();
        $openDeals = (clone $deals)->open()->count();
        $target = $user->monthly_target ?? 20;
        $winRate = ($wonDeals + $lostDeals) > 0
            ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1)
            : 0;

        $activities = Activity::where('user_id', $userId)->count();

        // Monthly member trend for this user (6 months)
        $monthlyWonCount = [];
        $now = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
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

        // Recent deals
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

        $bestCase = Deal::open()->count();
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

    // ─── Audit Logs ───────────────────────────────────

    public function getAuditLogs(array $filters = []): LengthAwarePaginator
    {
        return AuditLog::with('user')
            ->filterAction($filters['action'] ?? null)
            ->filterUser($filters['user_id'] ?? null)
            ->filterModule($filters['module'] ?? null)
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    // ─── Private Helpers ──────────────────────────────

    private function getMemberTrend(int $months): array
    {
        $data = [];
        $now = Carbon::now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $members = Deal::won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->count();

            $data[] = [
                'month' => $date->format('M'),
                'count' => $members,
            ];
        }

        return $data;
    }

    private function getFunnelData(string $period = 'all', ?string $startDate = null, ?string $endDate = null): array
    {
        $totalLeads = $this->applyPeriodFilter(Lead::query(), 'created_at', $period, $startDate, $endDate)->count();
        $qualified = $this->applyPeriodFilter(Lead::where('qualification', 'qualified'), 'created_at', $period, $startDate, $endDate)->count();
        $deals = $this->applyPeriodFilter(Deal::query(), 'created_at', $period, $startDate, $endDate)->count();
        $won = $this->applyPeriodFilter(Deal::won(), 'closed_at', $period, $startDate, $endDate)->count();

        return [
            ['label' => 'Leads', 'value' => $totalLeads, 'color' => '#3B82F6'],
            ['label' => 'Qualified', 'value' => $qualified, 'color' => '#8B5CF6'],
            ['label' => 'Deals', 'value' => $deals, 'color' => '#F59E0B'],
            ['label' => 'Won', 'value' => $won, 'color' => '#10B981'],
        ];
    }

    private function getSalesComparison(string $period = 'all', ?string $startDate = null, ?string $endDate = null): Collection
    {
        $salesUsers = User::role('Sales')->where('is_active', true)->get();

        return $salesUsers->map(fn($user) => [
            'name' => $user->name,
            'deals' => $this->applyPeriodFilter(Deal::where('assigned_to', $user->id), 'created_at', $period, $startDate, $endDate)->count(),
            'won' => $this->applyPeriodFilter(Deal::where('assigned_to', $user->id)->won(), 'closed_at', $period, $startDate, $endDate)->count(),
        ])->sortByDesc('won')->values();
    }

    private function getLeadSourcesMonthly(int $months): array
    {
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
            'this_week' => $query->whereBetween($column, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
            'this_month' => $query->whereYear($column, Carbon::now()->year)->whereMonth($column, Carbon::now()->month),
            'this_year' => $query->whereYear($column, Carbon::now()->year),
            default => $query,
        };
    }
}
