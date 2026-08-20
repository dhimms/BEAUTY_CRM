@extends('layouts.partials.app')
@section('title', 'Revenue Trend Report')
@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('manager.reports.index') }}" class="hover:text-amber-600">Reports</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Revenue Trend</li>
@endsection
@section('page-header', 'Revenue Trend Report')
@section('page-subtitle', 'Tren pendapatan perusahaan selama 12 bulan terakhir')
@section('page-actions')
    <form method="GET" action="{{ route('manager.reports.revenue') }}" class="flex items-center gap-2 text-sm">
        <select name="filter_month" class="px-3 py-2 border border-charcoal-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 bg-white">
            <option value="">Semua Bulan</option>
            @php
                $monthsList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            @endphp
            @foreach($monthsList as $index => $monthName)
                <option value="{{ $index + 1 }}" {{ request('filter_month') == ($index + 1) ? 'selected' : '' }}>{{ $monthName }}</option>
            @endforeach
        </select>
        <select name="filter_year" class="px-3 py-2 border border-charcoal-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 bg-white">
            <option value="">Pilih Tahun</option>
            @php $currentYear = date('Y'); @endphp
            @for($y = $currentYear + 1; $y >= $currentYear - 4; $y--)
                <option value="{{ $y }}" {{ request('filter_year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="px-4 py-2 bg-charcoal-900 text-white rounded-lg font-medium hover:bg-charcoal-800 transition-colors">Filter</button>
        @if(request('filter_year') && request('filter_month'))
            <a href="{{ route('manager.reports.revenue') }}" class="px-3 py-2 text-charcoal-500 hover:text-charcoal-700">Reset</a>
        @endif
    </form>
@endsection
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <x-kpi-card label="Total Pendapatan" :value="'Rp ' . number_format($revenueData['total'], 0, ',', '.')" color="emerald"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
    <x-kpi-card label="Rata-rata Pendapatan per Bulan" :value="'Rp ' . number_format($revenueData['average'], 0, ',', '.')" color="amber"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>' />
</div>

<x-card class="mb-8">
    <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Revenue Trend</h3>
    <div style="height: 300px;"><canvas id="revenueChart"></canvas></div>
</x-card>
<x-card :padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Bulan</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Pendapatan (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @foreach($revenueData['monthly'] as $m)
                    <tr class="hover:bg-charcoal-50/30">
                        <td class="px-6 py-3 text-charcoal-900 font-medium">{{ $m['month'] }}</td>
                        <td class="px-6 py-3 text-right text-charcoal-600 font-mono">{{ number_format($m['revenue'], 0, ',', '.') }}</td>
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
    const data = @json($revenueData['monthly']);
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: data.map(d => d.month_short),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: data.map(d => d.revenue),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true, tension: 0.4,
                pointBackgroundColor: '#10B981', pointRadius: 5, pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#F3F4F6' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
