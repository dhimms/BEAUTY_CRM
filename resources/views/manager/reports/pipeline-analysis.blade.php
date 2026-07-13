@extends('layouts.partials.app')
@section('title', 'Pipeline Analysis')
@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('manager.reports.index') }}" class="hover:text-amber-600">Reports</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Pipeline Analysis</li>
@endsection
@section('page-header', 'Pipeline Analysis Report')
@section('content')
<x-card>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left font-mono font-medium text-charcoal-500 uppercase">Stage</th>
                    <th class="px-6 py-3 text-right font-mono font-medium text-charcoal-500 uppercase">Total Deals</th>
                    <th class="px-6 py-3 text-right font-mono font-medium text-charcoal-500 uppercase">Total Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @foreach($pipelineData as $stage)
                    <tr class="hover:bg-charcoal-50/30">
                        <td class="px-6 py-4 font-medium text-charcoal-900">
                            <span class="w-3 h-3 rounded-full inline-block mr-2" style="background-color: {{ $stage['color'] }}"></span>
                            {{ $stage['name'] }}
                        </td>
                        <td class="px-6 py-4 text-right text-charcoal-600">{{ $stage['count'] }}</td>
                        <td class="px-6 py-4 text-right font-mono text-emerald-600">Rp {{ number_format($stage['total_value'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-card>
@endsection
