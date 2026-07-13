@extends('layouts.partials.app')
@section('title', 'Lead Sources')
@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('manager.reports.index') }}" class="hover:text-amber-600">Reports</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Lead Sources</li>
@endsection
@section('page-header', 'Lead Sources Report')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Leads per Source</h3>
        <div style="height: 300px;"><canvas id="sourcesChart"></canvas></div>
    </x-card>
    <x-card :padding="false">
        <div class="p-6 border-b border-charcoal-100">
            <h3 class="font-serif text-lg font-semibold text-charcoal-900">Detail</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-charcoal-50/50">
                        <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Source</th>
                        <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Leads</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal-100">
                    @foreach($sourcesData as $source)
                        <tr class="hover:bg-charcoal-50/30">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $source['color'] }}"></span>
                                    <span class="font-medium text-charcoal-900">{{ $source['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-right font-mono text-charcoal-700">{{ $source['count'] }}</td>
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
    const data = @json($sourcesData);
    new Chart(document.getElementById('sourcesChart'), {
        type: 'bar',
        data: {
            labels: data.map(d => d.name),
            datasets: [{ label: 'Leads', data: data.map(d => d.count), backgroundColor: data.map(d => d.color), borderRadius: 8 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, grid: { color: '#F3F4F6' } }, y: { grid: { display: false } } }
        }
    });
});
</script>
@endpush
