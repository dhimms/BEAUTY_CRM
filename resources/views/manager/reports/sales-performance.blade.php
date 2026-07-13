@extends('layouts.partials.app')
@section('title', 'Sales Performance')
@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('manager.reports.index') }}" class="hover:text-amber-600">Reports</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Sales Performance</li>
@endsection
@section('page-header', 'Sales Performance Report')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <x-kpi-card label="Total Revenue" :value="'Rp ' . number_format($salesData->sum('revenue'), 0, ',', '.')" color="emerald" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
    @php
        $totalWon = $salesData->sum('won');
        $avgDealVal = $totalWon > 0 ? $salesData->sum('revenue') / $totalWon : 0;
        $totalAvgDays = $salesData->where('won', '>', 0)->avg('avg_close_time') ?? 0;
    @endphp
    <x-kpi-card label="Average Deal Value" :value="'Rp ' . number_format($avgDealVal, 0, ',', '.')" color="amber" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>' />
    <x-kpi-card label="Total Activities" :value="$salesData->sum('activities')" color="blue" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>' />
    <x-kpi-card label="Avg Close Time" :value="round($totalAvgDays, 1) . ' Hari'" color="rose" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
</div>

<div class="mb-8">
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Performance Comparison</h3>
        <div style="height: 300px;"><canvas id="perfChart"></canvas></div>
    </x-card>
</div>
<x-card :padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-4 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Sales</th>
                    <th class="px-4 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Leads</th>
                    <th class="px-4 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Qualified</th>
                    <th class="px-4 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Deals</th>
                    <th class="px-4 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Won</th>
                    <th class="px-4 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Lost</th>
                    <th class="px-4 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Win Rate</th>
                    <th class="px-4 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Revenue</th>
                    <th class="px-4 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Avg Deal</th>
                    <th class="px-4 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Activities</th>
                    <th class="px-4 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Avg Close Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @foreach($salesData as $s)
                    <tr class="hover:bg-charcoal-50/30">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ $s['avatar_url'] }}" class="w-7 h-7 rounded-full">
                                <a href="{{ route('manager.team.show', $s['id']) }}" class="font-medium text-charcoal-900 hover:text-amber-600">{{ $s['name'] }}</a>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right text-charcoal-600">{{ $s['leads'] }}</td>
                        <td class="px-4 py-3 text-right text-charcoal-600">{{ $s['qualified'] }}</td>
                        <td class="px-4 py-3 text-right text-charcoal-600">{{ $s['deals'] }}</td>
                        <td class="px-4 py-3 text-right font-medium text-emerald-600">{{ $s['won'] }}</td>
                        <td class="px-4 py-3 text-right text-rose-500">{{ $s['lost'] }}</td>
                        <td class="px-4 py-3 text-right font-medium {{ $s['win_rate'] >= 50 ? 'text-emerald-600' : 'text-charcoal-500' }}">{{ $s['win_rate'] }}%</td>
                        <td class="px-4 py-3 text-right font-mono text-charcoal-900">Rp {{ number_format($s['revenue'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono text-charcoal-600">Rp {{ number_format($s['avg_deal_value'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-charcoal-600">{{ $s['activities'] }}</td>
                        <td class="px-4 py-3 text-right text-charcoal-600">{{ $s['avg_close_time'] }} Hari</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-card>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = @json($salesData);
    new Chart(document.getElementById('perfChart'), {
        type: 'bar',
        data: {
            labels: data.map(d => d.name),
            datasets: [
                { label: 'Won', data: data.map(d => d.won), backgroundColor: '#10B981', borderRadius: 6 },
                { label: 'Lost', data: data.map(d => d.lost), backgroundColor: '#EF4444', borderRadius: 6 },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { font: { size: 11, family: 'DM Sans' }, usePointStyle: true } } },
            scales: { y: { beginAtZero: true, grid: { color: '#F3F4F6' } }, x: { grid: { display: false } } }
        }
    });
});
</script>
@endpush
