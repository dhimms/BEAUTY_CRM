@extends('layouts.partials.app')
@section('title', 'Forecast & Target Penjualan')
@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Forecast</li>
@endsection
@section('page-header', 'Forecast & Target Penjualan')
@section('page-subtitle', 'Pantau proyeksi pendapatan masa depan dan performa pencapaian target tim Sales')

@section('content')
<div x-data="{ showTargetModal: false }" class="space-y-6">

    {{-- Top Action Bar --}}
    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-charcoal-100 shadow-sm">
        <h2 class="font-serif text-lg font-semibold text-charcoal-900">Ringkasan Pipeline Revenue</h2>
        <button @click="showTargetModal = true" class="inline-flex items-center gap-2 px-5 py-2.5 bg-charcoal-900 text-white text-sm font-medium rounded-lg hover:bg-charcoal-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Atur Target Bulanan
        </button>
    </div>

    {{-- KPI Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-kpi-card label="Actual Revenue (Bulan Ini)" :value="'Rp ' . number_format($revenueData['total_revenue_this_month'], 0, ',', '.')" color="emerald"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
        <x-kpi-card label="Projected Revenue" :value="'Rp ' . number_format($forecastData['total_projected'], 0, ',', '.')" color="amber"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>' />
        <x-kpi-card label="Best Case Pipeline" :value="'Rp ' . number_format($forecastData['best_case'], 0, ',', '.')" color="blue"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>' />
        <x-kpi-card label="Worst Case Pipeline" :value="'Rp ' . number_format($forecastData['worst_case'], 0, ',', '.')" color="rose"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>' />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Revenue Forecast Chart --}}
        <div class="xl:col-span-2">
            <x-card class="h-full">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-2">Projected vs Actual Revenue</h3>
                <p class="text-sm text-charcoal-500 mb-4">Projected Revenue = Nilai Deal × Probabilitas Tahap (Stage)</p>
                <div style="height: 350px;"><canvas id="forecastChart"></canvas></div>
            </x-card>
        </div>

        {{-- Detail Per Bulan Table --}}
        <div>
            <x-card :padding="false" class="h-full">
                <div class="p-6 border-b border-charcoal-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h3 class="font-serif text-lg font-semibold text-charcoal-900">Detail per Bulan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-charcoal-50/50">
                                <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Bulan</th>
                                <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Actual / Projected (Rp)</th>
                                <th class="px-6 py-3 text-center text-xs font-mono font-medium text-charcoal-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-charcoal-100">
                            @foreach($forecastData['months'] as $m)
                                <tr class="hover:bg-charcoal-50/30 transition-colors {{ !$m['is_past'] ? 'bg-amber-50/20' : '' }}">
                                    <td class="px-6 py-4 font-medium text-charcoal-900">{{ $m['month'] }}</td>
                                    <td class="px-6 py-4 text-right font-mono font-semibold {{ $m['is_past'] ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ number_format($m['is_past'] ? $m['actual'] : $m['projected'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
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
        </div>
    </div>

    {{-- Performa Sales Section --}}
    <div class="grid grid-cols-1 gap-6">
        <x-card :padding="false">
            <div class="p-6 border-b border-charcoal-100 flex justify-between items-center">
                <div>
                    <h3 class="font-serif text-lg font-bold text-charcoal-900">Performa Pencapaian Target Sales (Bulan Ini)</h3>
                    <p class="text-xs text-charcoal-500 mt-0.5">Monitoring perbandingan realisasi omset won terhadap target individual sales</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($revenueData['sales_performance'] as $sp)
                    <div class="p-4 border border-charcoal-200 rounded-xl bg-white hover:bg-charcoal-50/40 transition-all shadow-2xs flex flex-col justify-between space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ $sp['avatar_url'] }}" alt="{{ $sp['name'] }}" class="w-10 h-10 rounded-full object-cover border border-charcoal-200 shadow-2xs flex-shrink-0">
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-charcoal-900 truncate">{{ $sp['name'] }}</h4>
                                    <span class="text-[10px] text-charcoal-500 font-mono">{{ $sp['members_won_this_month'] }} Deals Won</span>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-full flex-shrink-0 {{ $sp['progress_raw'] >= 100 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                {{ $sp['progress_raw'] }}%
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-charcoal-100 text-xs">
                            <div>
                                <span class="text-[10px] font-mono text-charcoal-400 uppercase tracking-wider block">Realisasi</span>
                                <span class="font-mono font-bold text-emerald-600 block text-xs">
                                    Rp {{ number_format($sp['revenue_achieved'], 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-mono text-charcoal-400 uppercase tracking-wider block">Target</span>
                                <span class="font-mono font-semibold text-charcoal-700 block text-xs">
                                    Rp {{ number_format($sp['revenue_target'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="h-2 w-full bg-charcoal-100 rounded-full overflow-hidden flex">
                                <div class="h-full {{ $sp['progress_raw'] >= 100 ? 'bg-emerald-500' : 'bg-blue-600' }} rounded-full" style="width: {{ $sp['progress_percentage'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>

    {{-- Modal Atur Target Bulanan --}}
    <div x-show="showTargetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/40" @click="showTargetModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4" @click.stop>
            <div class="px-6 py-4 border-b border-charcoal-100 flex items-center justify-between">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Atur Target Pendapatan Bulanan</h3>
                <button @click="showTargetModal = false" class="text-charcoal-400 hover:text-charcoal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('manager.forecast.targets.update') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                    <div>
                        <label class="block text-sm font-medium text-charcoal-800 mb-1">Target Pendapatan (Untuk Semua Sales)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-charcoal-500 text-sm font-medium">Rp</span>
                            </div>
                            <input type="number" name="global_target" value="{{ $revenueData['sales_performance']->first()['revenue_target'] ?? 0 }}" min="0" step="1000" required class="w-full pl-10 px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300" placeholder="Contoh: 10000000">
                        </div>
                        <p class="text-xs text-charcoal-500 mt-2">Target ini akan diterapkan ke seluruh akun Sales secara otomatis.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-6 mt-4 border-t border-charcoal-100">
                    <button type="button" @click="showTargetModal = false" class="px-4 py-2 text-sm text-charcoal-600 hover:text-charcoal-800">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-charcoal-900 text-white text-sm font-medium rounded-xl hover:bg-charcoal-800 transition-colors">Simpan Target</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function formatRupiah(value) {
        if (value >= 1000000000) {
            return 'Rp ' + (value / 1000000000).toFixed(1).replace('.0', '') + ' Miliar';
        } else if (value >= 1000000) {
            return 'Rp ' + (value / 1000000).toFixed(1).replace('.0', '') + ' Jt';
        }
        return 'Rp ' + value.toLocaleString('id-ID');
    }

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
                },
                tooltip: { 
                    callbacks: { 
                        label: function(context) { 
                            return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID'); 
                        } 
                    } 
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => formatRupiah(v), font: { size: 11 } },
                    grid: { color: '#F3F4F6' }
                },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
});
</script>
@endpush
