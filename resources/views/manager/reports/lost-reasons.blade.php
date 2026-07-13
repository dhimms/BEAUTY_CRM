@extends('layouts.partials.app')
@section('title', 'Lost Reasons')
@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('manager.reports.index') }}" class="hover:text-amber-600">Reports</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Lost Reasons</li>
@endsection
@section('page-header', 'Lost Reasons Analysis')
@section('page-subtitle', 'Total deals lost: ' . $lostData['total'])
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Distribution</h3>
        <div class="flex items-center justify-center" style="height: 300px;">
            <canvas id="lostChart"></canvas>
        </div>
    </x-card>
    <x-card :padding="false">
        <div class="p-6 border-b border-charcoal-100">
            <h3 class="font-serif text-lg font-semibold text-charcoal-900">Detail</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-charcoal-50/50">
                        <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Alasan</th>
                        <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal-100">
                    @foreach($lostData['reasons'] as $reason)
                        <tr class="hover:bg-charcoal-50/30">
                            <td class="px-6 py-3 font-medium text-charcoal-900">{{ $reason['name'] }}</td>
                            <td class="px-6 py-3 text-right text-charcoal-600">{{ $reason['count'] }}</td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <div class="w-16 h-2 bg-charcoal-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-rose-500 rounded-full" style="width: {{ $reason['percentage'] }}%"></div>
                                    </div>
                                    <span class="text-charcoal-700 font-mono text-xs">{{ $reason['percentage'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = @json($lostData['reasons']);
    const colors = ['#EF4444', '#F59E0B', '#8B5CF6', '#3B82F6', '#EC4899', '#6B7280', '#10B981'];
    new Chart(document.getElementById('lostChart'), {
        type: 'doughnut',
        data: {
            labels: data.map(d => d.name),
            datasets: [{ data: data.map(d => d.count), backgroundColor: colors.slice(0, data.length), borderWidth: 0, hoverOffset: 8 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '55%',
            plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, font: { size: 11, family: 'DM Sans' } } } }
        }
    });
});
</script>
@endpush
