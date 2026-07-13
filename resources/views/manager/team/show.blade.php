@extends('layouts.partials.app')
@section('title', 'Performa — ' . $memberData['user']->name)
@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('manager.team.index') }}" class="hover:text-amber-600">Team</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">{{ $memberData['user']->name }}</li>
@endsection

@section('content')
{{-- Profile Header --}}
<div class="bg-white rounded-xl border border-charcoal-200 shadow-sm p-6 mb-6">
    <div class="flex items-center gap-4">
        <img src="{{ $memberData['user']->avatar_url }}" alt="{{ $memberData['user']->name }}" class="w-16 h-16 rounded-2xl border-2 border-amber-200">
        <div>
            <h1 class="font-serif text-2xl font-semibold text-charcoal-900">{{ $memberData['user']->name }}</h1>
            <p class="text-charcoal-500 text-sm">{{ $memberData['user']->email }} • {{ $memberData['user']->phone }}</p>
        </div>
    </div>
</div>

{{-- KPI Grid --}}
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
    @php
        $kpis = [
            ['label' => 'Leads', 'value' => $memberData['leads'], 'color' => 'amber'],
            ['label' => 'Qualified', 'value' => $memberData['qualified'], 'color' => 'purple'],
            ['label' => 'Deals', 'value' => $memberData['deals'], 'color' => 'blue'],
            ['label' => 'Won', 'value' => $memberData['won'], 'color' => 'emerald'],
            ['label' => 'Lost', 'value' => $memberData['lost'], 'color' => 'rose'],
            ['label' => 'Win Rate', 'value' => $memberData['win_rate'] . '%', 'color' => 'amber'],
        ];
    @endphp
    @foreach($kpis as $kpi)
        <div class="bg-white rounded-xl border border-charcoal-200 shadow-sm p-4 text-center hover:shadow-md transition-shadow">
            <p class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">{{ $kpi['label'] }}</p>
            <p class="text-2xl font-serif font-semibold text-charcoal-900 mt-1">{{ $kpi['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Revenue Summary --}}
    <x-card>
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-serif text-lg font-semibold text-charcoal-900">Total Revenue</h3>
        </div>
        <p class="text-3xl font-serif font-bold text-amber-700 mb-1">Rp {{ number_format($memberData['revenue'], 0, ',', '.') }}</p>
        <p class="text-sm text-charcoal-500">Avg deal value: <span class="font-medium text-charcoal-700">Rp {{ number_format($memberData['avg_deal_value'], 0, ',', '.') }}</span></p>
        <p class="text-sm text-charcoal-500 mt-1">Total aktivitas: <span class="font-medium text-charcoal-700">{{ $memberData['activities'] }}</span></p>
    </x-card>

    {{-- Monthly Revenue Chart --}}
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Revenue (6 Bulan)</h3>
        <div style="height: 200px;"><canvas id="memberRevenueChart"></canvas></div>
    </x-card>
</div>

{{-- Recent Deals --}}
<x-card :padding="false">
    <div class="p-6 border-b border-charcoal-100">
        <h3 class="font-serif text-lg font-semibold text-charcoal-900">10 Deal Terakhir</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Deal</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Lead</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Stage</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Value</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($memberData['recent_deals'] as $deal)
                    <tr class="hover:bg-charcoal-50/30 transition-colors">
                        <td class="px-6 py-3 font-medium text-charcoal-900">{{ $deal->name }}</td>
                        <td class="px-6 py-3 text-charcoal-600">{{ $deal->lead?->name ?? '-' }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $deal->pipelineStage?->color ?? '#6B7280' }}"></span>
                                {{ $deal->pipelineStage?->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right font-mono text-charcoal-900">{{ $deal->formatted_value }}</td>
                        <td class="px-6 py-3"><x-badge :color="$deal->status_color" size="xs">{{ ucfirst($deal->status) }}</x-badge></td>
                        <td class="px-6 py-3 text-charcoal-500 text-xs">{{ $deal->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-charcoal-400">Belum ada deal.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyData = @json($memberData['monthly_revenue']);
    new Chart(document.getElementById('memberRevenueChart'), {
        type: 'bar',
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [{
                label: 'Revenue',
                data: monthlyData.map(d => d.revenue),
                backgroundColor: '#D97706',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(0) + 'M' }, grid: { color: '#F3F4F6' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
