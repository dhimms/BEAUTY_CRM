@extends('layouts.partials.app')
@section('title', 'Admin Dashboard')
@section('page-header', 'Dashboard')
@section('page-subtitle')
Overview of BeautyCRM performance — {{ now()->translatedFormat("F Y") }}
@endsection

@section('content')
<div class="space-y-6">
    {{-- KPI Cards dengan Trend Arrow --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Leads This Month --}}
        <div class="bg-white rounded-xl border border-charcoal-200 shadow-sm p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-mono uppercase tracking-wider text-charcoal-500 mb-1">Leads Bulan Ini</p>
                    <p class="text-3xl font-serif font-bold text-charcoal-900">{{ $kpi['totalLeads']['value'] }}</p>
                </div>
                <div class="p-2.5 bg-blue-50 rounded-xl text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs font-medium {{ $kpi['totalLeads']['up'] ? 'text-emerald-600' : 'text-rose-600' }}">
                @if($kpi['totalLeads']['up'])
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @endif
                {{ abs($kpi['totalLeads']['trend']) }}% vs bulan lalu
            </div>
        </div>

        {{-- Active Deals --}}
        <div class="bg-white rounded-xl border border-charcoal-200 shadow-sm p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-mono uppercase tracking-wider text-charcoal-500 mb-1">Active Deals</p>
                    <p class="text-3xl font-serif font-bold text-charcoal-900">{{ $kpi['activeDeals']['value'] }}</p>
                </div>
                <div class="p-2.5 bg-amber-50 rounded-xl text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs font-medium {{ $kpi['activeDeals']['up'] ? 'text-emerald-600' : 'text-rose-600' }}">
                @if($kpi['activeDeals']['up'])
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @endif
                {{ abs($kpi['activeDeals']['trend']) }}% vs bulan lalu
            </div>
        </div>

        {{-- Won This Month --}}
        <div class="bg-white rounded-xl border border-charcoal-200 shadow-sm p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-mono uppercase tracking-wider text-charcoal-500 mb-1">Won This Month</p>
                    <p class="text-3xl font-serif font-bold text-charcoal-900">{{ $kpi['wonThisMonth']['value'] }}</p>
                </div>
                <div class="p-2.5 bg-emerald-50 rounded-xl text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs font-medium {{ $kpi['wonThisMonth']['up'] ? 'text-emerald-600' : 'text-rose-600' }}">
                @if($kpi['wonThisMonth']['up'])
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @endif
                {{ abs($kpi['wonThisMonth']['trend']) }}% vs bulan lalu
            </div>
        </div>

        {{-- [FITUR BARU] Member Baru Bulan Ini (Menggantikan Kartu KPI Revenue sebelumnya) --}}
        <div class="bg-white rounded-xl border border-charcoal-200 shadow-sm p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-mono uppercase tracking-wider text-charcoal-500 mb-1">Member Baru Bulan Ini</p>
                    <p class="text-3xl font-serif font-bold text-charcoal-900">{{ number_format($kpi['newMembers']['value']) }}</p>
                </div>
                <div class="p-2.5 bg-indigo-50 rounded-xl text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs font-medium {{ $kpi['newMembers']['up'] ? 'text-emerald-600' : 'text-rose-600' }}">
                @if($kpi['newMembers']['up'])
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @endif
                {{ abs($kpi['newMembers']['trend']) }}% vs bulan lalu
            </div>
        </div>
    </div>

    {{-- Global Target Banner --}}
    <x-card class="border border-emerald-100 shadow-sm bg-white overflow-hidden relative">
        <div class="absolute right-0 top-0 w-64 h-full bg-gradient-to-l from-emerald-50/50 to-transparent pointer-events-none"></div>
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-serif text-lg font-semibold text-charcoal-900 leading-tight">Target Revenue Penjualan</h3>
                    <p class="text-xs text-charcoal-500 mt-0.5">Realisasi Omset Won vs Target Manager Bulan Ini</p>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:flex gap-6 lg:gap-10">
                <div>
                    <p class="text-[10px] font-mono tracking-wider uppercase text-charcoal-400 mb-1">Target Per Sales</p>
                    <p class="font-medium text-charcoal-900">Rp {{ number_format($globalTargetData['target_per_sales'], 0, ',', '.') }}</p>
                </div>
                <div class="pl-4 lg:pl-0 border-l lg:border-l-0 lg:border-r lg:pr-10 border-charcoal-100">
                    <p class="text-[10px] font-mono tracking-wider uppercase text-charcoal-400 mb-1">Total Target Tim</p>
                    <p class="font-medium text-charcoal-900">Rp {{ number_format($globalTargetData['total_target'], 0, ',', '.') }} <span class="text-xs text-charcoal-400 font-normal">({{ $globalTargetData['total_sales'] }} Sales)</span></p>
                </div>
                <div>
                    <p class="text-[10px] font-mono tracking-wider uppercase text-charcoal-400 mb-1">Realisasi (Won)</p>
                    <p class="font-medium text-emerald-600">Rp {{ number_format($globalTargetData['realisasi'], 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-mono tracking-wider uppercase text-charcoal-400 mb-1">Sisa Target</p>
                    @if($globalTargetData['is_tercapai'])
                        <p class="font-medium text-emerald-600">Rp 0 <span class="text-xs text-emerald-500 font-normal">(Tercapai)</span></p>
                    @else
                        <p class="font-medium text-amber-600">Rp {{ number_format($globalTargetData['sisa_target'], 0, ',', '.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="relative">
            <div class="flex justify-between items-end mb-2">
                <span class="text-xs font-medium text-charcoal-600">Pencapaian Target Tim <span class="text-charcoal-400 font-normal">({{ $globalTargetData['total_sales'] }} Sales Aktif)</span></span>
                <span class="text-sm font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">{{ $globalTargetData['percent_raw'] }}% Total Achieved</span>
            </div>
            <div class="w-full bg-charcoal-100 rounded-full h-3 overflow-hidden">
                <div class="h-3 rounded-full bg-emerald-500 transition-all duration-1000" style="width: {{ $globalTargetData['percent'] }}%"></div>
            </div>
        </div>
    </x-card>

    {{-- Charts Row 1: Lead Trend + Leads by Source --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card class="lg:col-span-2">
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Lead Trend (6 Bulan Terakhir)</h3>
            <canvas id="leadTrendChart" height="100"></canvas>
        </x-card>

        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Leads by Source</h3>
            <canvas id="leadsBySourceChart" height="200"></canvas>
        </x-card>
    </div>

    {{-- Pipeline Summary Bar Chart --}}
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Pipeline Summary — Open Deals per Stage</h3>
        @if($pipelineSummary->isEmpty())
            <p class="text-center text-charcoal-500 py-6">Tidak ada pipeline stage yang terdaftar.</p>
        @else
            <canvas id="pipelineChart" height="60"></canvas>
        @endif
    </x-card>

    {{-- Tables Row: Recent Activities + Top Sales --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Activities --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Recent Activities <span class="text-sm font-sans font-normal text-charcoal-400">(10 Terbaru)</span></h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-charcoal-500 uppercase bg-charcoal-50 border-b border-charcoal-200">
                        <tr>
                            <th class="px-4 py-3 font-medium">User</th>
                            <th class="px-4 py-3 font-medium">Action</th>
                            <th class="px-4 py-3 font-medium">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-charcoal-100">
                        @forelse($recentActivities as $activity)
                            <tr class="hover:bg-charcoal-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $activity->user->avatar_url }}" alt="" class="w-6 h-6 rounded-full">
                                        <span class="text-charcoal-900 font-medium">{{ $activity->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-charcoal-600">
                                    <x-badge :color="$activity->type_color">{{ $activity->type }}</x-badge>
                                    <span class="ml-2 text-xs truncate max-w-xs block text-charcoal-500">{{ $activity->subject }}</span>
                                </td>
                                <td class="px-4 py-3 text-charcoal-500 text-xs whitespace-nowrap">{{ $activity->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-charcoal-500">Belum ada aktivitas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Top Sales --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Top Performing Sales <span class="text-sm font-sans font-normal text-charcoal-400">(Bulan Ini)</span></h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-charcoal-500 uppercase bg-charcoal-50 border-b border-charcoal-200">
                        <tr>
                            <th class="px-4 py-3 font-medium">#</th>
                            <th class="px-4 py-3 font-medium">Sales Rep</th>
                            <th class="px-4 py-3 font-medium text-right">Revenue (Won)</th>
                            <th class="px-4 py-3 font-medium text-left w-1/3">Pencapaian Target</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-charcoal-100">
                        @forelse($topSales as $i => $sales)
                            <tr class="hover:bg-charcoal-50">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                        {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-charcoal-100 text-charcoal-600' : 'bg-charcoal-50 text-charcoal-500') }}">
                                        {{ $i + 1 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-charcoal-900 font-medium">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $sales->avatar_url }}" alt="{{ $sales->name }}" class="w-7 h-7 rounded-full">
                                        {{ $sales->name }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-emerald-600 whitespace-nowrap">
                                    Rp {{ number_format($sales->revenue_this_month ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $target = $sales->revenue_target ?? 4000000;
                                        $achieved = $sales->revenue_this_month ?? 0;
                                        $pct = $target > 0 ? min(100, round(($achieved / $target) * 100)) : 0;
                                        $color = $pct >= 100 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-amber-500' : 'bg-rose-500');
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <div class="w-full bg-charcoal-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-1.5 rounded-full {{ $color }} transition-all duration-1000" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-xs font-mono font-medium {{ $pct >= 100 ? 'text-emerald-600' : 'text-charcoal-500' }} w-8 text-right">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-charcoal-500">Belum ada data penjualan bulan ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ─── Lead Trend Line Chart ────────────────────
        const trendCtx = document.getElementById('leadTrendChart').getContext('2d');
        const trendData = @json($leadTrend);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.map(d => d.label),
                datasets: [{
                    label: 'New Leads',
                    data: trendData.map(d => d.count),
                    borderColor: '#F43F5E',
                    backgroundColor: 'rgba(244, 63, 94, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#F43F5E',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)', borderDash: [3, 3] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // ─── Leads By Source Doughnut ─────────────────
        const sourceCtx = document.getElementById('leadsBySourceChart').getContext('2d');
        const sourceData = @json($leadsBySource);

        new Chart(sourceCtx, {
            type: 'doughnut',
            data: {
                labels: sourceData.map(d => d.label),
                datasets: [{
                    data: sourceData.map(d => d.count),
                    backgroundColor: ['#F43F5E', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#64748B'],
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } } },
                cutout: '65%',
            }
        });

        // ─── Pipeline Bar Chart ───────────────────────
        const pipelineCanvas = document.getElementById('pipelineChart');
        if (pipelineCanvas) {
            const pipelineCtx = pipelineCanvas.getContext('2d');
            const pipelineData = @json($pipelineSummary);

            new Chart(pipelineCtx, {
                type: 'bar',
                data: {
                    labels: pipelineData.map(d => d.label),
                    datasets: [{
                        label: 'Open Deals',
                        data: pipelineData.map(d => d.count),
                        backgroundColor: pipelineData.map(d => d.color + 'CC'),
                        borderColor: pipelineData.map(d => d.color),
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.raw} open deal${ctx.raw !== 1 ? 's' : ''}`
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                        y: { grid: { display: false } }
                    }
                }
            });
        }
    });
</script>
@endpush
