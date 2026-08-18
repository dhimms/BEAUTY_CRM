<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {   // berarti mengambil daata secara keselusruhan (dari awal hingga akhir ) dari tabel lead
        $now  = now(); 
        // berarti mengambil data bulan sebelumnya 
        $prev = now()->subMonth();
 
        // ─── KPI This Month ──────────────────────────────────── , KPI singkatan dari Key Performance Indicator (Indikator Kinerja Utama)
        // menghitung total lead berdasarkn bulan dan tahun saat ini
        $totalLeads       = Lead::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        // menghitung total lead berdasarkn bulan dan tahun sebelumnya
        $totalLeadsPrev   = Lead::whereMonth('created_at', $prev->month)->whereYear('created_at', $prev->year)->count();
        
        // ===> perhatian <=== 
        // ===> function open dan won adalah method yang sudah kita buat di model deal
        // ===> jadi function itu memfilter data sesuai kondisi yang sudah di tentukan
        // ===> misal function open adalah method yang memfilter data sesuai kondisi "status = open"
        // ===> dan function won adalah method yang memfilter data sesuai kondisi "status = won"

        // menghitung total active deals
        $activeDeals      = Deal::open()->count();
        // menghitung total active deals bulan sebelumnya
        $activeDealsPrev  = Deal::open()->whereMonth('created_at', $prev->month)->whereYear('created_at', $prev->year)->count();
        
        // menghitung total won deals bulan ini
        $wonThisMonth     = Deal::won()
            ->whereMonth('closed_at', $now->month)
            ->whereYear('closed_at', $now->year)
            ->count();
        // menghitung total won deals bulan sebelumnya
        $wonLastMonth     = Deal::won()
            ->whereMonth('closed_at', $prev->month)
            ->whereYear('closed_at', $prev->year)
            ->count();

        // [FITUR BARU] Menghitung total member baru bulan ini berdasarkan bulan dan tahun saat ini
        $newMembersThisMonth = Customer::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();
        // [FITUR BARU] Menghitung total member baru bulan sebelumnya
        $newMembersLastMonth = Customer::whereMonth('created_at', $prev->month)
            ->whereYear('created_at', $prev->year)
            ->count();

        // Helper: hitung persentase trend
        // membuat rumus untuk menghitung presentase perubahan dari bulan sebelumnya
        // rumus: ((nilai sekarang - nilai bulan sebelumnya) / nilai bulan sebelumnya) * 100 dan (,1 ini diguakan agar nilai presentase hanya diambil 1 angka di belakang koma)
        $trendPercent = fn($curr, $prev) => $prev > 0
            ? round((($curr - $prev) / $prev) * 100, 1)
            : ($curr > 0 ? 100 : 0);

        // KPI (Key Performance Indicator)
        // value ini menyimpan angka asli dari total leads bulan ini
        // trend ini menyimpan presentase 
        // up ini menyimpan nilai boolean apakah total leads bulan ini lebih besar dari bulan sebelumnya (dan juga memunculkan nilai panah naik atau turun)
        $kpi = [
            'totalLeads'   => ['value' => $totalLeads,       'trend' => $trendPercent($totalLeads, $totalLeadsPrev),       'up' => $totalLeads >= $totalLeadsPrev],
            'activeDeals'  => ['value' => $activeDeals,      'trend' => $trendPercent($activeDeals, $activeDealsPrev),     'up' => $activeDeals >= $activeDealsPrev],
            'wonThisMonth' => ['value' => $wonThisMonth,     'trend' => $trendPercent($wonThisMonth, $wonLastMonth),       'up' => $wonThisMonth >= $wonLastMonth],
            // [FITUR BARU] Menyimpan data member baru ke dalam array KPI (menggantikan KPI revenue sebelumnya)
            'newMembers'   => ['value' => $newMembersThisMonth, 'trend' => $trendPercent($newMembersThisMonth, $newMembersLastMonth), 'up' => $newMembersThisMonth >= $newMembersLastMonth],
        ];

        // ─── Lead Trend (6 bulan terakhir) ────────────────────
        $leadTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            // mementukan bulan apa saja yang akan di ambil datanya
            // dengan cara mengurangi bulan saat ini dengan bulan sebanyak variabel $i (dimana i mulai dari 5 hingga 0)
            $month = $now->copy()->subMonths($i);
            $leadTrend[] = [ 
                // $month->translatedFormat('M Y') ini adalah format tanggal
                // 'M' adalah singkatan dari bulan (contoh: Jan, Feb, Mar)
                // 'Y' adalah singkatan dari tahun (contoh: 2022, 2023, 2024)
                'label' => $month->translatedFormat('M Y'),
                // menghitung total lead berdasarkan bulan dan tahun
                // function whereYear() adalah function yang digunakan untuk memfilter data berdasarkan tahun
                // function whereMonth() adalah function yang digunakan untuk memfilter data berdasarkan bulan
                'count' => Lead::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        // ─── Leads by Source (doughnut chart) ───────────────── 
        // pada bagian ini kita hanya mengamvil id nya saja dan tidak mengambil semua fieldnya
        // DB::raw('count(*) as total') digunakan untuk menghitung jumlah lead berdasarkan lead_source_id
        // disini menggunakan DB::raw karena dari eloquentnya nggk ada untuk menjumlahkan value berdasarkan group
        $leadsBySource = Lead::select('lead_source_id', DB::raw('count(*) as total'))
            ->with('source')       
            ->groupBy('lead_source_id') 
            ->get()
            // map digunakan untuk membongkar isi wadah satu persatu (karena kita memanggilnya dengan method get) 
            ->map(fn($item) => [
                // mengambil data nama source (leadSource) 
                'label' => $item->source?->name ?? 'Unknown', 
                // mengambil data total lead berdasarkan nama source
                'count' => $item->total,
            ]);

        // ─── Pipeline Summary (bar chart) ─────────────────────
        // melakukan perhitungan deals berdasarkan pipeline stage dengan menggunakan relasi deals
        // dan hitung yang hanya statusnya open
        $pipelineSummary = PipelineStage::withCount(['deals' => fn($q) => $q->where('status', 'open')]) 
            // orderBy('order') adalah method yang digunakan untuk mengurutkan data berdasarkan order
            // method ini 'order' sudah kita buat di model
            ->orderBy('order') 
            ->get() 
            ->map(fn($stage) => [
                'label' => $stage->name,
                'count' => $stage->deals_count,
                'color' => $stage->color ?? '#F43F5E',
            ]);

        // ─── Recent Activities (10 terbaru) ───────────────────
        // mengambil data activity terbaru 
        // method with() digunakan untuk melakukan eager loading (mengambil data relasi)
        // method latest() digunakan untuk mengurutkan data berdasarkan created_at secara descending
        // method limit() digunakan untuk membatasi jumlah data yang diambil
        $recentActivities = Activity::with(['user', 'activitable'])
            ->latest()
            ->limit(10)
            ->get();

        // ─── Top Sales (won deals bulan ini) ──────────────────
        // mengambil data user yang memiliki role 'Sales'
        $topSales = User::role('Sales')
        // menghitung ada berapa banyak transaksi (assignedDeals) milik Sales ini
        // berdasarkan status won dan bulan dan tahun
            ->withCount(['assignedDeals as won_this_month' => fn($q) => $q
                ->won()
                ->whereMonth('closed_at', $now->month)
                ->whereYear('closed_at', $now->year)
            ])
            // [FITUR BARU] Perhitungan total revenue (withSum) telah dihapus dari tabel Top Sales ini, karena kita hanya menampilkan jumlah won deals.
            // orderByDesc('won_this_month') adalah method yang digunakan untuk mengurutkan data berdasarkan won_this_month
            // method ini 'won_this_month' sudah kita buat di model
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

