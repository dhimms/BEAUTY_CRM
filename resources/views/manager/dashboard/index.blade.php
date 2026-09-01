@extends('layouts.partials.app')

@section('title', 'Manager Dashboard')

@section('page-header', 'Manager Dashboard')
@section('page-subtitle', 'Ringkasan performa tim sales & bisnis')
@section('page-actions')
    <form method="GET" action="{{ route('manager.dashboard') }}" class="flex items-center gap-2" x-data="{ period: '{{ request('period', 'all') }}' }">
        <select name="period" x-model="period" @change="if (period !== 'custom' && period !== 'month_year') $el.form.submit()" class="px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white font-medium text-charcoal-700">
            <option value="all">Semua Waktu</option>
            <option value="today">Hari Ini</option>
            <option value="this_week">Minggu Ini</option>
            <option value="this_month">Bulan Ini</option>
            <option value="this_year">Tahun Ini</option>
            <option value="month_year">Pilih Bulan & Tahun</option>
            <option value="custom">Pilih Tanggal</option>
        </select>
        
        <div x-show="period === 'month_year'" x-cloak class="flex items-center gap-2">
            <select name="filter_month" class="px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white" :required="period === 'month_year'">
                @php
                    $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                @endphp
                @foreach($months as $index => $month)
                    <option value="{{ $index + 1 }}" {{ request('filter_month', \Carbon\Carbon::now()->month) == ($index + 1) ? 'selected' : '' }}>{{ $month }}</option>
                @endforeach
            </select>
            <select name="filter_year" class="px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white" :required="period === 'month_year'">
                @php $currentYear = \Carbon\Carbon::now()->year; @endphp
                @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                    <option value="{{ $y }}" {{ request('filter_year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-3 py-2 bg-charcoal-900 text-white rounded-xl text-sm font-medium hover:bg-charcoal-800 transition-colors">Terapkan</button>
        </div>

        <div x-show="period === 'custom'" x-cloak class="flex items-center gap-2">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white" :required="period === 'custom'">
            <span class="text-charcoal-400 font-medium">-</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white" :required="period === 'custom'">
            <button type="submit" class="px-3 py-2 bg-charcoal-900 text-white rounded-xl text-sm font-medium hover:bg-charcoal-800 transition-colors">Terapkan</button>
        </div>
    </form>
@endsection

@section('content')
{{-- KPI Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <x-kpi-card label="Total Pendapatan" :value="'Rp ' . number_format($totalRevenue, 0, ',', '.')" color="emerald"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' />

    <div class="bg-white rounded-xl border border-charcoal-200 shadow-sm p-6 hover:shadow-md transition-shadow">
        <p class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">Target Pendapatan</p>
        <div class="flex items-center gap-4 mt-2">
            <div class="relative w-16 h-16 flex-shrink-0">
                <canvas id="targetAchievementChart"></canvas>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-sm font-bold text-emerald-700">{{ $targetAchievementRaw }}%</span>
                </div>
            </div>
            <div class="flex flex-col flex-1 min-w-0">
                <span class="text-[10px] text-charcoal-500 uppercase tracking-wider">Target Tim:</span>
                @php
                    $formattedTarget = 'Rp ' . number_format($totalRevenueTarget, 0, ',', '.');
                    if ($totalRevenueTarget >= 1000000000) {
                        $formattedTarget = 'Rp ' . rtrim(rtrim(number_format($totalRevenueTarget / 1000000000, 1, ',', '.'), '0'), ',') . ' Miliar';
                    } elseif ($totalRevenueTarget >= 1000000) {
                        $formattedTarget = 'Rp ' . rtrim(rtrim(number_format($totalRevenueTarget / 1000000, 1, ',', '.'), '0'), ',') . ' Jt';
                    }
                @endphp
                <span class="text-sm font-semibold text-charcoal-800 truncate" title="Rp {{ number_format($totalRevenueTarget, 0, ',', '.') }} / {{ $activeSalesCount }} Orang">
                    {{ $formattedTarget }} / {{ $activeSalesCount }} Org
                </span>
            </div>
        </div>
    </div>

    <x-kpi-card label="Total Deals" :value="number_format($totalDeals)" color="blue"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>' />

    <x-kpi-card label="Barang Paling Laris" :value="$topProduct" color="purple"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>' />
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Revenue Trend --}}
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Trend Pendapatan (12 Bulan)</h3>
        <div style="height: 280px;"><canvas id="revenueTrendChart"></canvas></div>
    </x-card>

    {{-- Sales Funnel --}}
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Sales Funnel</h3>
        <div class="space-y-3 py-4">
            @foreach($funnel as $i => $stage)
                @php
                    $maxVal = $funnel[0]['value'] ?: 1;
                    $width = max(20, ($stage['value'] / $maxVal) * 100);
                    $conversionRate = $i > 0 && $funnel[$i - 1]['value'] > 0
                        ? round(($stage['value'] / $funnel[$i - 1]['value']) * 100, 1)
                        : 100;
                @endphp
                <div class="relative">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-charcoal-700">{{ $stage['label'] }}</span>
                        <span class="text-sm font-mono text-charcoal-500">{{ number_format($stage['value']) }}
                            @if($i > 0) <span class="text-xs text-charcoal-400">({{ $conversionRate }}%)</span> @endif
                        </span>
                    </div>
                    <div class="h-8 bg-charcoal-100 rounded-lg overflow-hidden">
                        <div class="h-full rounded-lg transition-all duration-500" style="width: {{ $width }}%; background-color: {{ $stage['color'] }};"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Sales Performance Comparison --}}
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Performa Sales</h3>
        <div style="height: 280px;"><canvas id="salesComparisonChart"></canvas></div>
    </x-card>

    {{-- Lead Sources by Month --}}
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Sumber Leads (6 Bulan)</h3>
        <div style="height: 280px;"><canvas id="leadSourcesChart"></canvas></div>
    </x-card>
</div>

{{-- Leaderboard --}}
<x-card :padding="false">
    <div class="p-6 border-b border-charcoal-100">
        <div class="flex items-center justify-between">
            <h3 class="font-serif text-lg font-semibold text-charcoal-900">Peringkat Tim Sales</h3>
            <a href="{{ route('manager.team.index') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">Detail →</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Sales</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Target (Rp)</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Pendapatan (Rp)</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Pencapaian (%)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($leaderboard as $index => $member)
                    <tr class="hover:bg-charcoal-50/30 transition-colors">
                        <td class="px-6 py-4">
                            @if($index === 0)
                                <span class="text-amber-500 font-bold text-lg">🥇</span>
                            @elseif($index === 1)
                                <span class="text-gray-400 font-bold text-lg">🥈</span>
                            @elseif($index === 2)
                                <span class="text-amber-700 font-bold text-lg">🥉</span>
                            @else
                                <span class="text-charcoal-400 font-mono">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $member['avatar_url'] }}" alt="" class="w-8 h-8 rounded-full">
                                <span class="font-medium text-charcoal-900">{{ $member['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-charcoal-600">{{ number_format($member['revenue_target'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right font-medium font-mono text-emerald-600">{{ number_format($member['revenue_achieved'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-medium {{ $member['progress_percentage'] >= 100 ? 'text-emerald-600' : 'text-amber-500' }}">{{ $member['progress_percentage'] }}%</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-charcoal-400">Belum ada data sales.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const warmPalette = ['#F59E0B', '#D97706', '#B45309', '#92400E', '#78350F', '#EF4444', '#3B82F6'];

    function formatRupiah(value) {
        if (value >= 1000000000) {
            return 'Rp ' + (value / 1000000000).toFixed(1).replace('.0', '') + ' Miliar';
        } else if (value >= 1000000) {
            return 'Rp ' + (value / 1000000).toFixed(1).replace('.0', '') + ' Jt';
        }
        return 'Rp ' + value.toLocaleString('id-ID');
    }

    // Target Achievement Doughnut
    new Chart(document.getElementById('targetAchievementChart'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [{{ $targetAchievement }}, {{ max(0, 100 - $targetAchievement) }}],
                backgroundColor: ['#10B981', '#F3F4F6'],
                borderWidth: 0,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
    });

    // Revenue Trend Area Chart
    const revenueTrend = @json($revenueTrend);
    new Chart(document.getElementById('revenueTrendChart'), {
        type: 'line',
        data: {
            labels: revenueTrend.map(d => d.month),
            datasets: [{
                label: 'Pendapatan',
                data: revenueTrend.map(d => d.revenue),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10B981',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: { callbacks: { label: function(context) { return 'Rp ' + context.parsed.y.toLocaleString('id-ID'); } } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 11 }, callback: function(value) { return formatRupiah(value); } }, grid: { color: '#F3F4F6' } },
                x: { ticks: { font: { size: 11 } }, grid: { display: false } }
            }
        }
    });

    // Sales Comparison (Target as Line, Achieved as Bar)
    const salesComp = @json($salesComparison);
    new Chart(document.getElementById('salesComparisonChart'), {
        type: 'bar',
        data: {
            labels: salesComp.map(d => d.name),
            datasets: [
                { type: 'line', label: 'Target Pendapatan', data: salesComp.map(d => d.target_revenue), borderColor: '#F59E0B', borderWidth: 2, borderDash: [5, 5], pointRadius: 0, fill: false },
                { type: 'bar', label: 'Pendapatan Dicapai', data: salesComp.map(d => d.achieved_revenue), backgroundColor: '#10B981', borderRadius: 6 },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { 
                legend: { labels: { font: { size: 11, family: 'DM Sans' }, usePointStyle: true } },
                tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID'); } } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: function(value) { return formatRupiah(value); } }, grid: { color: '#F3F4F6' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Lead Sources Stacked Bar
    const lsData = @json($leadSourcesMonthly);
    new Chart(document.getElementById('leadSourcesChart'), {
        type: 'bar',
        data: {
            labels: lsData.labels,
            datasets: lsData.datasets.map((ds, i) => ({
                label: ds.label,
                data: ds.data,
                backgroundColor: ds.color,
                borderRadius: 4,
            }))
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { font: { size: 10, family: 'DM Sans' }, usePointStyle: true, padding: 12 } } },
            scales: {
                x: { stacked: true, grid: { display: false } },
                y: { stacked: true, beginAtZero: true, grid: { color: '#F3F4F6' } }
            }
        }
    });
});
</script>
@endpush
