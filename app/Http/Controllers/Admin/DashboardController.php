<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();

        // KPI Cards
        $totalLeads       = Lead::count();
        $activeDeals      = Deal::open()->count();
        $wonThisMonth     = Deal::won()
            ->whereMonth('closed_at', $now->month)
            ->whereYear('closed_at', $now->year)
            ->count();
        $revenueThisMonth = Deal::won()
            ->whereMonth('closed_at', $now->month)
            ->whereYear('closed_at', $now->year)
            ->sum('value');

        // Lead trend last 6 months (for Chart.js)
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

        // Leads by source (for doughnut chart)
        $leadsBySource = Lead::select('lead_source_id', DB::raw('count(*) as total'))
            ->with('source')
            ->groupBy('lead_source_id')
            ->get()
            ->map(fn($item) => [
                'label' => $item->source?->name ?? 'Unknown',
                'count' => $item->total,
            ]);

        // Recent activities (5 latest)
        $recentActivities = Activity::with(['user', 'activitable'])
            ->latest()
            ->limit(5)
            ->get();

        // Top performing sales (won deals this month)
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
            'totalLeads',
            'activeDeals',
            'wonThisMonth',
            'revenueThisMonth',
            'leadTrend',
            'leadsBySource',
            'recentActivities',
            'topSales'
        ));
    }
}
