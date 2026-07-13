@extends('layouts.partials.app')

@section('title', 'Reports')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Reports</li>
@endsection

@section('page-header', 'Report Center')
@section('page-subtitle', 'Analisis performa bisnis secara detail')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @php
        $reports = [
            ['route' => 'manager.reports.sales-performance', 'title' => 'Sales Performance', 'desc' => 'Analisis performa setiap sales person', 'icon' => 'M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'amber'],
            ['route' => 'manager.reports.revenue', 'title' => 'Revenue Report', 'desc' => 'Tren pendapatan bulanan', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
            ['route' => 'manager.reports.lost-reasons', 'title' => 'Lost Reasons', 'desc' => 'Analisis alasan deal gagal', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636', 'color' => 'rose'],
            ['route' => 'manager.reports.lead-sources', 'title' => 'Lead Sources', 'desc' => 'Performa sumber lead', 'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1', 'color' => 'blue'],
            ['route' => 'manager.reports.pipeline-analysis', 'title' => 'Pipeline Analysis', 'desc' => 'Analisa tahapan pipeline', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'amber'],
            ['route' => 'manager.reports.team-activity', 'title' => 'Team Activity Report', 'desc' => 'Log aktivitas tim sales', 'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'color' => 'blue'],
        ];
    @endphp

    @foreach($reports as $report)
        <a href="{{ route($report['route']) }}" class="group">
            <div class="bg-white rounded-xl border border-charcoal-200 shadow-sm p-6 hover:shadow-lg hover:border-amber-300 transition-all h-full">
                <div class="w-12 h-12 rounded-xl bg-{{ $report['color'] }}-50 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-{{ $report['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $report['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-1">{{ $report['title'] }}</h3>
                <p class="text-sm text-charcoal-500">{{ $report['desc'] }}</p>
            </div>
        </a>
    @endforeach
</div>

{{-- Export Section --}}
<x-card>
    <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Export Report</h3>
    <form method="GET" action="{{ route('manager.reports.export') }}" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1">Tipe Report</label>
            <select name="report_type" required class="px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-amber-500">
                <option value="sales-performance">Sales Performance</option>
                <option value="revenue">Revenue</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1">Format</label>
            <select name="format" class="px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-amber-500">
                <option value="xlsx">Excel (.xlsx)</option>
                <option value="csv">CSV (.csv)</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-amber-600 text-white rounded-xl text-sm font-medium hover:bg-amber-700 transition-colors shadow-sm">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download
        </button>
    </form>
</x-card>
@endsection
