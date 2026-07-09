@extends('layouts.partials.app')
@section('title', 'Forecast')
@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Forecast</li>
@endsection
@section('page-header', 'Revenue Forecast')
@section('page-subtitle', 'Proyeksi vs aktual revenue berdasarkan weighted deal value')

@section('content')
{{-- KPI Summary --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <x-kpi-card label="Total Actual Revenue" :value="'Rp ' . number_format($forecastData['total_actual'], 0, ',', '.')" color="emerald"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
    <x-kpi-card label="Total Projected Revenue" :value="'Rp ' . number_format($forecastData['total_projected'], 0, ',', '.')" color="amber"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>' />
</div>

{{-- Forecast Chart --}}
<x-card class="mb-8">
    <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-2">Projected vs Actual Revenue</h3>
    <p class="text-sm text-charcoal-500 mb-4">Projected = Deal Value × Stage Probability</p>
    <div style="height: 350px;"><canvas id="forecastChart"></canvas></div>
</x-card>

{{-- Forecast Table --}}
<x-card :padding="false">
    <div class="p-6 border-b border-charcoal-100">
        <h3 class="font-serif text-lg font-semibold text-charcoal-900">Detail per Bulan</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Bulan</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Actual</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Projected</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Periode</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @foreach($forecastData['months'] as $m)
                    <tr class="hover:bg-charcoal-50/30 transition-colors {{ !$m['is_past'] ? 'bg-amber-50/20' : '' }}">
                        <td class="px-6 py-3 font-medium text-charcoal-900">{{ $m['month'] }}</td>
                        <td class="px-6 py-3 text-right font-mono {{ $m['actual'] > 0 ? 'text-emerald-600 font-semibold' : 'text-charcoal-400' }}">
                            Rp {{ number_format($m['actual'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-right font-mono {{ $m['projected'] > 0 ? 'text-amber-600 font-semibold' : 'text-charcoal-400' }}">
                            Rp {{ number_format($m['projected'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3">
                            @if($m['is_past'])
                                <x-badge color="gray" size="xs">Past</x-badge>
                            @else
                                <x-badge color="amber" size="xs">Future</x-badge>
                            @endif
                        </td>
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
    const data = @json($forecastData['months']);
    new Chart(document.getElementById('forecastChart'), {
        type: 'bar',
        data: {
            labels: data.map(d => d.month_short),
            datasets: [
                {
                    label: 'Actual Revenue',
                    data: data.map(d => d.actual),
                    backgroundColor: '#10B981',
                    borderRadius: 6,
                    order: 2,
                },
                {
                    label: 'Projected Revenue',
                    data: data.map(d => d.projected),
                    backgroundColor: 'rgba(217, 119, 6, 0.4)',
                    borderColor: '#D97706',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    borderRadius: 6,
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { font: { size: 11, family: 'DM Sans' }, usePointStyle: true, padding: 16 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => 'Rp ' + (v / 1000000).toFixed(0) + 'M', font: { size: 11 } },
                    grid: { color: '#F3F4F6' }
                },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
});
</script>
@endpush
