<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now  = now();
        $prev = now()->subMonth();

        // ─── KPI This Month ────────────────────────────────────
        $totalLeads       = Lead::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $totalLeadsPrev   = Lead::whereMonth('created_at', $prev->month)->whereYear('created_at', $prev->year)->count();

        $activeDeals      = Deal::open()->count();
        $activeDealsPrev  = Deal::open()->whereMonth('created_at', $prev->month)->whereYear('created_at', $prev->year)->count();

        $wonThisMonth     = Deal::won()
            ->whereMonth('closed_at', $now->month)
            ->whereYear('closed_at', $now->year)
            ->count();
        $wonLastMonth     = Deal::won()
            ->whereMonth('closed_at', $prev->month)
            ->whereYear('closed_at', $prev->year)
            ->count();

        $revenueThisMonth = Deal::won()
            ->whereMonth('closed_at', $now->month)
            ->whereYear('closed_at', $now->year)
            ->sum('value');
        $revenueLastMonth = Deal::won()
            ->whereMonth('closed_at', $prev->month)
            ->whereYear('closed_at', $prev->year)
            ->sum('value');

        // Helper: hitung persentase trend
        $trendPercent = fn($curr, $prev) => $prev > 0
            ? round((($curr - $prev) / $prev) * 100, 1)
            : ($curr > 0 ? 100 : 0);

        $kpi = [
            'totalLeads'   => ['value' => $totalLeads,       'trend' => $trendPercent($totalLeads, $totalLeadsPrev),       'up' => $totalLeads >= $totalLeadsPrev],
            'activeDeals'  => ['value' => $activeDeals,      'trend' => $trendPercent($activeDeals, $activeDealsPrev),     'up' => $activeDeals >= $activeDealsPrev],
            'wonThisMonth' => ['value' => $wonThisMonth,     'trend' => $trendPercent($wonThisMonth, $wonLastMonth),       'up' => $wonThisMonth >= $wonLastMonth],
            'revenue'      => ['value' => $revenueThisMonth, 'trend' => $trendPercent($revenueThisMonth, $revenueLastMonth), 'up' => $revenueThisMonth >= $revenueLastMonth],
        ];

        // ─── Lead Trend (6 bulan terakhir) ────────────────────
        $leadTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $leadTrend[] = [
                'label' => $month->translatedFormat('M Y'),
                'count' => Lead::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        // ─── Leads by Source (doughnut chart) ─────────────────
        $leadsBySource = Lead::select('lead_source_id', DB::raw('count(*) as total'))
            ->with('source')
            ->groupBy('lead_source_id')
            ->get()
            ->map(fn($item) => [
                'label' => $item->source?->name ?? 'Unknown',
                'count' => $item->total,
            ]);

        // ─── Pipeline Summary (bar chart) ─────────────────────
        $pipelineSummary = PipelineStage::withCount(['deals' => fn($q) => $q->where('status', 'open')])
            ->ordered()
            ->get()
            ->map(fn($stage) => [
                'label' => $stage->name,
                'count' => $stage->deals_count,
                'color' => $stage->color ?? '#F43F5E',
            ]);

        // ─── Recent Activities (10 terbaru) ───────────────────
        $recentActivities = Activity::with(['user', 'activitable'])
            ->latest()
            ->limit(10)
            ->get();

        // ─── Top Sales (won deals bulan ini) ──────────────────
        $topSales = User::role('Sales')
            ->withCount(['assignedDeals as won_this_month' => fn($q) => $q
                ->won()
                ->whereMonth('closed_at', $now->month)
                ->whereYear('closed_at', $now->year)
            ])
            ->withSum(['assignedDeals as revenue_this_month' => fn($q) => $q
                ->won()
                ->whereMonth('closed_at', $now->month)
                ->whereYear('closed_at', $now->year)
            ], 'value')
            ->orderByDesc('won_this_month')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'kpi',
            'leadTrend',
            'leadsBySource',
            'pipelineSummary',
            'recentActivities',
            'topSales'
        ));
    }
}

