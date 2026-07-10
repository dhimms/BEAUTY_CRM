<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // ─── KPI Cards ─────────────────────────────────
        $myLeadsCount = Lead::where('assigned_to', $userId)
            ->whereNotIn('status', ['closed', 'converted'])
            ->count();

        $myDealsCount = Deal::where('assigned_to', $userId)
            ->where('status', 'open')
            ->count();

        $wonThisMonth = Deal::where('assigned_to', $userId)
            ->where('status', 'won')
            ->whereMonth('closed_at', now()->month)
            ->whereYear('closed_at', now()->year)
            ->count();

        $myRevenue = Deal::where('assigned_to', $userId)
            ->where('status', 'won')
            ->whereMonth('closed_at', now()->month)
            ->whereYear('closed_at', now()->year)
            ->sum('value');

        // ─── Today's Follow-ups ─────────────────────────
        $todayFollowUps = Activity::where('user_id', $userId)
            ->where('follow_up_status', 'pending')
            ->whereNotNull('follow_up_date')
            ->where('follow_up_date', '<=', now()->toDateString())
            ->with('activitable')
            ->orderBy('follow_up_date')
            ->limit(10)
            ->get();

        // ─── Pipeline Summary ───────────────────────────
        $pipelineSummary = PipelineStage::ordered()
            ->withCount(['deals' => function ($q) use ($userId) {
                $q->where('assigned_to', $userId)->where('status', 'open');
            }])
            ->withSum(['deals' => function ($q) use ($userId) {
                $q->where('assigned_to', $userId)->where('status', 'open');
            }], 'value')
            ->get();

        // ─── Upcoming Follow-ups (next 7 days) ─────────
        $upcomingFollowUps = Activity::where('user_id', $userId)
            ->where('follow_up_status', 'pending')
            ->whereNotNull('follow_up_date')
            ->where('follow_up_date', '>', now()->toDateString())
            ->where('follow_up_date', '<=', now()->addDays(7)->toDateString())
            ->with('activitable')
            ->orderBy('follow_up_date')
            ->limit(10)
            ->get();

        // ─── Recent Activities ──────────────────────────
        $recentActivities = Activity::where('user_id', $userId)
            ->with('activitable')
            ->latest('activity_date')
            ->limit(5)
            ->get();

        // ─── Target vs Actual ───────────────────────────
        $monthlyTarget = 50000000; // Rp 50jt default target
        $monthlyActual = $myRevenue;
        $targetPercent = $monthlyTarget > 0
            ? min(100, round(($monthlyActual / $monthlyTarget) * 100))
            : 0;

        return view('sales.dashboard.index', compact(
            'myLeadsCount',
            'myDealsCount',
            'wonThisMonth',
            'myRevenue',
            'todayFollowUps',
            'pipelineSummary',
            'upcomingFollowUps',
            'recentActivities',
            'monthlyTarget',
            'monthlyActual',
            'targetPercent'
        ));
    }
}