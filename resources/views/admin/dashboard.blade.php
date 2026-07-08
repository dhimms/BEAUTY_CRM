@extends('layouts.partials.app')
@section('title', 'Admin Dashboard')
@section('page-header', 'Dashboard')
@section('page-subtitle', 'Overview of BeautyCRM performance')

@section('content')
<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-kpi-card label="Total Leads" value="{{ $totalLeads }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' />" color="blue" />
        <x-kpi-card label="Active Deals" value="{{ $activeDeals }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' />" color="amber" />
        <x-kpi-card label="Won This Month" value="{{ $wonThisMonth }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7' />" color="emerald" />
        <x-kpi-card label="Revenue This Month" value="Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' />" color="rose" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Line Chart --}}
        <x-card class="lg:col-span-2">
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Lead Trend (6 Months)</h3>
            <canvas id="leadTrendChart" height="100"></canvas>
        </x-card>

        {{-- Doughnut Chart --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Leads by Source</h3>
            <canvas id="leadsBySourceChart" height="200"></canvas>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Activities --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Recent Activities</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-charcoal-500 uppercase bg-charcoal-50 border-b border-charcoal-200">
                        <tr>
                            <th class="px-4 py-3 font-medium">User</th>
                            <th class="px-4 py-3 font-medium">Action</th>
                            <th class="px-4 py-3 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-charcoal-100">
                        @forelse($recentActivities as $activity)
                            <tr class="hover:bg-charcoal-50">
                                <td class="px-4 py-3 text-charcoal-900 font-medium">{{ $activity->user->name }}</td>
                                <td class="px-4 py-3 text-charcoal-600">
                                    <x-badge :color="$activity->type_color">{{ $activity->type }}</x-badge>
                                    <span class="ml-2 text-xs truncate">{{ $activity->subject }}</span>
                                </td>
                                <td class="px-4 py-3 text-charcoal-500">{{ $activity->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-center text-charcoal-500">No activities found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Top Sales --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Top Performing Sales (This Month)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-charcoal-500 uppercase bg-charcoal-50 border-b border-charcoal-200">
                        <tr>
                            <th class="px-4 py-3 font-medium">Sales Rep</th>
                            <th class="px-4 py-3 font-medium text-center">Won Deals</th>
                            <th class="px-4 py-3 font-medium text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-charcoal-100">
                        @forelse($topSales as $sales)
                            <tr class="hover:bg-charcoal-50">
                                <td class="px-4 py-3 text-charcoal-900 font-medium flex items-center gap-2">
                                    <img src="{{ $sales->avatar_url }}" alt="{{ $sales->name }}" class="w-6 h-6 rounded-full">
                                    {{ $sales->name }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-emerald-600">{{ $sales->won_this_month }}</td>
                                <td class="px-4 py-3 text-right text-charcoal-700">Rp {{ number_format($sales->revenue_this_month, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-center text-charcoal-500">No sales data found</td>
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
        // Lead Trend Chart
        const trendCtx = document.getElementById('leadTrendChart').getContext('2d');
        const trendData = @json($leadTrend);
        
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.reverse().map(d => d.label),
                datasets: [{
                    label: 'New Leads',
                    data: trendData.map(d => d.count),
                    borderColor: '#F43F5E',
                    backgroundColor: 'rgba(244, 63, 94, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Leads By Source Chart
        const sourceCtx = document.getElementById('leadsBySourceChart').getContext('2d');
        const sourceData = @json($leadsBySource);
        
        new Chart(sourceCtx, {
            type: 'doughnut',
            data: {
                labels: sourceData.map(d => d.label),
                datasets: [{
                    data: sourceData.map(d => d.count),
                    backgroundColor: [
                        '#F43F5E', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#64748B'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
@endpush
