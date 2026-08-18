<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;

class DashboardController extends Controller
{
    /**
     * Muncul di: Menu Sidebar -> "Dashboard" (/sales/dashboard)
     * Tampilan: resources/views/sales/dashboard/index.blade.php
     * Penjelasan: Menyiapkan ringkasan data statistik KPI Sales, grafik pipeline, follow-up hari ini & 7 hari ke depan, serta aktivitas terbaru.
     */
    public function index()
    {
        $userId = auth()->id();

        // ─── KPI Cards ─────────────────────────────────
        //menampilkan total lead yang belum di follow up
        $myLeadsCount = Lead::where('assigned_to', $userId)
            ->whereNotIn('status', ['closed', 'converted'])
            ->count();
        //menampilkan total deal yang belum di follow up
        $myDealsCount = Deal::where('assigned_to', $userId)
            ->where('status', 'open')
            ->count();
        //menampilkan total deal yang dimenangkan bulan ini
        $wonThisMonth = Deal::where('assigned_to', $userId)
            ->where('status', 'won')
            ->whereMonth('closed_at', now()->month)
            ->whereYear('closed_at', now()->year)
            ->count();

        // ─── Today's Follow-ups ─────────────────────────
        //menampilkan aktivitas follow up hari ini
        $todayFollowUps = Activity::where('user_id', $userId)
            ->where('follow_up_status', 'pending')
            ->whereNotNull('follow_up_date')
            ->where('follow_up_date', '<=', now()->toDateString())
            ->with('activitable')
            ->orderBy('follow_up_date')
            ->limit(10)
            ->get();

        // ─── Pipeline Summary ───────────────────────────
        //menampilkan jumlah deal di setiap tahapan pipeline(grafik)
        $pipelineSummary = PipelineStage::ordered()
            ->withCount(['deals' => function ($q) use ($userId) {
                $q->where('assigned_to', $userId)->where('status', 'open');
            }])
            ->get();

        // ─── Upcoming Follow-ups (next 7 days) ─────────
        //menampilkan aktivitas follow up 7 hari ke depan
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
        //menampilkan aktivitas terakhir 5 aktivitas
        $recentActivities = Activity::where('user_id', $userId)
            ->with('activitable')
            ->latest('activity_date')
            ->limit(5)
            ->get();

        // ─── Target vs Actual (Jumlah Member) ───────────
        //menampilkan target vs aktual (jumlah member)
        $monthlyTarget = auth()->user()->monthly_target ?? 20;
        $monthlyActual = $wonThisMonth;
        $targetPercent = $monthlyTarget > 0
            ? min(100, round(($monthlyActual / $monthlyTarget) * 100))
            : 0;
        //mengirim data ke tampian blade
        return view('sales.dashboard.index', compact(
            'myLeadsCount',
            'myDealsCount',
            'wonThisMonth',
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