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

    public function getManagerDashboard(): array
    {
        $totalLeads = Lead::count();
        $totalDeals = Deal::count();
        $wonDeals = Deal::won()->count();
        $lostDeals = Deal::lost()->count();
        $winRate = ($wonDeals + $lostDeals) > 0
            ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1)
            : 0;
        $totalRevenue = Deal::won()->sum('value');

        // Revenue trend 12 months
        $revenueTrend = $this->getRevenueTrend(12);

        // Funnel data
        $funnel = $this->getFunnelData();

        // Sales performance comparison
        $salesComparison = $this->getSalesComparison();

        // Lead sources breakdown by month
        $leadSourcesMonthly = $this->getLeadSourcesMonthly(6);

        // Leaderboard
        $leaderboard = $this->getTeamLeaderboard();

        return compact(
            'totalLeads',
            'totalDeals',
            'wonDeals',
            'winRate',
            'totalRevenue',
            'revenueTrend',
            'funnel',
            'salesComparison',
            'leadSourcesMonthly',
            'leaderboard'
        );
    }

    // ─── Pipeline ─────────────────────────────────────

    public function getPipelineData(): array
    {
        $stages = PipelineStage::ordered()->get();

        $pipeline = [];
        foreach ($stages as $stage) {
            $deals = Deal::with(['lead', 'assignedUser'])
                ->where('pipeline_stage_id', $stage->id)
                ->where('status', 'open')
                ->orderBy('expected_close_date')
                ->get()
                ->map(fn($deal) => [
                    'id' => $deal->id,
                    'name' => $deal->name,
                    'value' => $deal->value,
                    'formatted_value' => $deal->formatted_value,
                    'weighted_value' => $deal->weighted_value,
                    'lead_name' => $deal->lead?->name ?? '-',
                    'assigned_to' => $deal->assignedUser?->name ?? '-',
                    'expected_close' => $deal->expected_close_date?->format('d M Y'),
                    'status' => $deal->status,
                ]);

            $pipeline[] = [
                'id' => $stage->id,
                'name' => $stage->name,
                'color' => $stage->color,
                'probability' => $stage->probability,
                'deals' => $deals,
                'total_value' => $deals->sum('value'),
                'count' => $deals->count(),
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
            $revenue = (clone $deals)->won()->sum('value');
            $avgDealValue = $wonDeals > 0 ? $revenue / $wonDeals : 0;
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
                'revenue' => $revenue,
                'avg_deal_value' => $avgDealValue,
                'activities' => $activities,
                'avg_close_time' => $avgCloseTime,
            ];
        })->sortByDesc('revenue')->values();
    }

    // ─── Revenue Report ───────────────────────────────

    public function getRevenueReport(int $months = 12): array
    {
        $data = [];
        $now = Carbon::now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $revenue = Deal::won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->sum('value');

            $dealsCount = Deal::won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->count();

            $data[] = [
                'month' => $date->format('M Y'),
                'month_short' => $date->format('M'),
                'revenue' => (float) $revenue,
                'deals_count' => $dealsCount,
            ];
        }

        $totalRevenue = array_sum(array_column($data, 'revenue'));
        $avgMonthly = $months > 0 ? $totalRevenue / $months : 0;

        return [
            'monthly' => $data,
            'total' => $totalRevenue,
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

    public function getTeamLeaderboard(): Collection
    {
        $salesUsers = User::role('Sales')->where('is_active', true)->get();

        return $salesUsers->map(function ($user) {
            $wonDeals = Deal::where('assigned_to', $user->id)->won()->count();
            $lostDeals = Deal::where('assigned_to', $user->id)->lost()->count();
            $revenue = Deal::where('assigned_to', $user->id)->won()->sum('value');
            $leads = Lead::where('assigned_to', $user->id)->count();
            $winRate = ($wonDeals + $lostDeals) > 0
                ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1)
                : 0;

            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->avatar_url,
                'leads' => $leads,
                'won' => $wonDeals,
                'revenue' => $revenue,
                'win_rate' => $winRate,
            ];
        })->sortByDesc('revenue')->values();
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
        $revenue = (clone $deals)->won()->sum('value');
        $avgDealValue = $wonDeals > 0 ? $revenue / $wonDeals : 0;
        $winRate = ($wonDeals + $lostDeals) > 0
            ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 1)
            : 0;

        $activities = Activity::where('user_id', $userId)->count();

        // Monthly revenue trend for this user (6 months)
        $monthlyRevenue = [];
        $now = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $rev = Deal::where('assigned_to', $userId)
                ->won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->sum('value');
            $monthlyRevenue[] = [
                'month' => $date->format('M'),
                'revenue' => (float) $rev,
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
            'revenue' => $revenue,
            'avg_deal_value' => $avgDealValue,
            'win_rate' => $winRate,
            'activities' => $activities,
            'monthly_revenue' => $monthlyRevenue,
            'recent_deals' => $recentDeals,
        ];
    }

    // ─── Forecast ─────────────────────────────────────

    public function getForecastData(): array
    {
        $now = Carbon::now();
        $months = [];

        for ($i = 5; $i >= -6; $i--) {
            $date = $now->copy()->subMonths($i);
            $label = $date->format('M Y');
            $isPast = $date->lt($now->copy()->startOfMonth());

            // Actual revenue (won deals closed in this month)
            $actual = Deal::won()
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->sum('value');

            // Projected revenue (open deals expected to close in this month × stage probability)
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

        $bestCase = Deal::open()->sum('value');
        $closingStageId = PipelineStage::where('name', 'like', '%Closing%')->value('id');
        $worstCase = $closingStageId ? Deal::open()->where('pipeline_stage_id', $closingStageId)->sum('value') : 0;

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
                'revenue' => (float) $revenue,
            ];
        }

        return $data;
    }

    private function getFunnelData(): array
    {
        $totalLeads = Lead::count();
        $qualified = Lead::where('qualification', 'qualified')->count();
        $deals = Deal::count();
        $won = Deal::won()->count();

        return [
            ['label' => 'Leads', 'value' => $totalLeads, 'color' => '#3B82F6'],
            ['label' => 'Qualified', 'value' => $qualified, 'color' => '#8B5CF6'],
            ['label' => 'Deals', 'value' => $deals, 'color' => '#F59E0B'],
            ['label' => 'Won', 'value' => $won, 'color' => '#10B981'],
        ];
    }

    private function getSalesComparison(): Collection
    {
        $salesUsers = User::role('Sales')->where('is_active', true)->get();

        return $salesUsers->map(fn($user) => [
            'name' => $user->name,
            'deals' => Deal::where('assigned_to', $user->id)->count(),
            'revenue' => (float) Deal::where('assigned_to', $user->id)->won()->sum('value'),
        ])->sortByDesc('revenue')->values();
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
}
