@extends('layouts.partials.app')

@section('title', 'Manager Dashboard')

@section('page-header', 'Manager Dashboard')
@section('page-subtitle', 'Ringkasan performa tim sales & bisnis')

@section('content')
{{-- KPI Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <x-kpi-card label="Total Leads" :value="number_format($totalLeads)" color="amber"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>' />

    <x-kpi-card label="Total Deals" :value="number_format($totalDeals)" color="blue"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>' />

    <x-kpi-card label="Won Deals" :value="number_format($wonDeals)" color="emerald"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>' />

    <div class="bg-white rounded-xl border border-charcoal-200 shadow-sm p-6 hover:shadow-md transition-shadow">
        <p class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">Win Rate</p>
        <div class="flex items-center gap-4 mt-2">
            <div class="relative w-16 h-16">
                <canvas id="winRateChart"></canvas>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-sm font-bold text-amber-700">{{ $winRate }}%</span>
                </div>
            </div>
        </div>
    </div>

    <x-kpi-card label="Revenue" :value="'Rp ' . number_format($totalRevenue, 0, ',', '.')" color="rose"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Revenue Trend --}}
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Revenue Trend (12 Bulan)</h3>
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
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Sales Performance</h3>
        <div style="height: 280px;"><canvas id="salesComparisonChart"></canvas></div>
    </x-card>

    {{-- Lead Sources by Month --}}
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Lead Sources (6 Bulan)</h3>
        <div style="height: 280px;"><canvas id="leadSourcesChart"></canvas></div>
    </x-card>
</div>

{{-- Leaderboard --}}
<x-card :padding="false">
    <div class="p-6 border-b border-charcoal-100">
        <div class="flex items-center justify-between">
            <h3 class="font-serif text-lg font-semibold text-charcoal-900">Sales Team Leaderboard</h3>
            <a href="{{ route('manager.team.index') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">Detail →</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Sales</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Leads</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Won</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Revenue</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Win Rate</th>
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
                        <td class="px-6 py-4 text-right text-charcoal-600">{{ $member['leads'] }}</td>
                        <td class="px-6 py-4 text-right font-medium text-emerald-600">{{ $member['won'] }}</td>
                        <td class="px-6 py-4 text-right font-mono text-charcoal-900">Rp {{ number_format($member['revenue'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-medium {{ $member['win_rate'] >= 50 ? 'text-emerald-600' : 'text-charcoal-500' }}">{{ $member['win_rate'] }}%</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-charcoal-400">Belum ada data sales.</td></tr>
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

    // Win Rate Doughnut
    new Chart(document.getElementById('winRateChart'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [{{ $winRate }}, {{ 100 - $winRate }}],
                backgroundColor: ['#D97706', '#F3F4F6'],
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
                label: 'Revenue',
                data: revenueTrend.map(d => d.revenue),
                borderColor: '#D97706',
                backgroundColor: 'rgba(217, 119, 6, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#D97706',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(0) + 'M', font: { size: 11 } }, grid: { color: '#F3F4F6' } },
                x: { ticks: { font: { size: 11 } }, grid: { display: false } }
            }
        }
    });

    // Sales Comparison Dual Bar
    const salesComp = @json($salesComparison);
    new Chart(document.getElementById('salesComparisonChart'), {
        type: 'bar',
        data: {
            labels: salesComp.map(d => d.name),
            datasets: [
                { label: 'Deals', data: salesComp.map(d => d.deals), backgroundColor: '#F59E0B', borderRadius: 6 },
                { label: 'Revenue (Jt)', data: salesComp.map(d => d.revenue / 1000000), backgroundColor: '#D97706', borderRadius: 6 },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { font: { size: 11, family: 'DM Sans' }, usePointStyle: true } } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#F3F4F6' } },
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
