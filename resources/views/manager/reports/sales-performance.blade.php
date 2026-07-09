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
