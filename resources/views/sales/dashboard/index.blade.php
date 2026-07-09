@extends('layouts.partials.app')

@section('title', 'Sales Dashboard')

@section('breadcrumb')
    <li class="text-charcoal-400">
        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a2 2 0 110-4 2 2 0 010 4z"/>
        </svg>
        Dashboard
    </li>
@endsection

@section('page-header', 'Sales Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name . '!')

@section('content')
<div class="space-y-6">

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <x-kpi-card label="My Leads" :value="$myLeadsCount" color="blue"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>' />

        <x-kpi-card label="My Deals" :value="$myDealsCount" color="purple"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>' />

        <x-kpi-card label="Won This Month" :value="$wonThisMonth" color="emerald"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>' />

        <x-kpi-card label="My Revenue" :value="'Rp ' . number_format($myRevenue, 0, ',', '.')" color="amber"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
    </div>

    {{-- Target vs Actual + Pipeline Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Target vs Actual --}}
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Target Bulan Ini</h3>
                <span class="text-xs font-mono text-charcoal-400 uppercase">{{ now()->format('F Y') }}</span>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-charcoal-600">Actual: <strong class="text-charcoal-900">Rp {{ number_format($monthlyActual, 0, ',', '.') }}</strong></span>
                    <span class="text-charcoal-600">Target: <strong class="text-charcoal-900">Rp {{ number_format($monthlyTarget, 0, ',', '.') }}</strong></span>
                </div>
                <div class="w-full bg-charcoal-100 rounded-full h-4 overflow-hidden">
                    <div class="h-4 rounded-full transition-all duration-500 {{ $targetPercent >= 100 ? 'bg-emerald-500' : ($targetPercent >= 70 ? 'bg-blue-500' : ($targetPercent >= 40 ? 'bg-amber-500' : 'bg-rose-500')) }}"
                         style="width: {{ $targetPercent }}%"></div>
                </div>
                <div class="text-center">
                    <span class="text-2xl font-serif font-bold {{ $targetPercent >= 100 ? 'text-emerald-600' : 'text-blue-600' }}">{{ $targetPercent }}%</span>
                    <p class="text-xs text-charcoal-400 mt-0.5">dari target tercapai</p>
                </div>
            </div>
        </x-card>

        {{-- Pipeline Summary --}}
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Pipeline Summary</h3>
                <a href="{{ route('sales.deals.pipeline') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat Pipeline →</a>
            </div>
            <div class="space-y-3">
                @foreach($pipelineSummary as $stage)
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $stage->color }}"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-charcoal-700 truncate">{{ $stage->name }}</span>
                                <span class="text-xs font-mono text-charcoal-500">{{ $stage->deals_count }} deal{{ $stage->deals_count != 1 ? 's' : '' }}</span>
                            </div>
                            <div class="w-full bg-charcoal-100 rounded-full h-1.5">
                                @php $maxVal = (float)$pipelineSummary->max('deals_sum_value') ?: 1; @endphp
                                <div class="h-1.5 rounded-full transition-all" style="width: {{ ((float)$stage->deals_sum_value / $maxVal) * 100 }}%; background-color: {{ $stage->color }}"></div>
                            </div>
                        </div>
                        <span class="text-xs font-medium text-charcoal-600 whitespace-nowrap">Rp {{ number_format($stage->deals_sum_value ?? 0, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            {{-- Mini Chart --}}
            <div class="mt-4 flex justify-center">
                <canvas id="pipelineChart" width="160" height="160"></canvas>
            </div>
        </x-card>
    </div>

    {{-- Today's Follow-ups + Upcoming Follow-ups --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Today's Follow-ups --}}
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">
                    Follow-up Hari Ini
                    @if($todayFollowUps->count() > 0)
                        <span class="ml-2 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-rose-500 rounded-full">{{ $todayFollowUps->count() }}</span>
                    @endif
                </h3>
            </div>
            @if($todayFollowUps->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-charcoal-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-charcoal-400">Tidak ada follow-up hari ini 🎉</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($todayFollowUps as $followUp)
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-amber-50/50 border border-amber-100 hover:bg-amber-50 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-{{ $followUp->type_color }}-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                @if($followUp->follow_up_type === 'call')
                                    <svg class="w-4 h-4 text-{{ $followUp->type_color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                @elseif($followUp->follow_up_type === 'whatsapp')
                                    <svg class="w-4 h-4 text-{{ $followUp->type_color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                @elseif($followUp->follow_up_type === 'email')
                                    <svg class="w-4 h-4 text-{{ $followUp->type_color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-{{ $followUp->type_color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-charcoal-800 truncate">{{ $followUp->subject ?: $followUp->follow_up_notes }}</p>
                                <p class="text-xs text-charcoal-500 mt-0.5">
                                    @if($followUp->activitable)
                                        {{ class_basename($followUp->activitable_type) }}: {{ $followUp->activitable->name ?? '-' }}
                                    @endif
                                </p>
                            </div>
                            <form action="{{ route('sales.activities.complete-followup', $followUp) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs px-2.5 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-colors font-medium">
                                    Done
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        {{-- Upcoming Follow-ups --}}
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Upcoming Follow-ups</h3>
                <span class="text-xs font-mono text-charcoal-400">7 hari ke depan</span>
            </div>
            @if($upcomingFollowUps->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-charcoal-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-charcoal-400">Belum ada follow-up terjadwal</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-charcoal-100">
                                <th class="text-left py-2 text-xs font-mono text-charcoal-400 uppercase">Tanggal</th>
                                <th class="text-left py-2 text-xs font-mono text-charcoal-400 uppercase">Tipe</th>
                                <th class="text-left py-2 text-xs font-mono text-charcoal-400 uppercase">Subject</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-charcoal-50">
                            @foreach($upcomingFollowUps as $upcoming)
                                <tr class="hover:bg-charcoal-50/50 transition-colors">
                                    <td class="py-2.5 font-mono text-xs text-charcoal-600">{{ $upcoming->follow_up_date->format('d M') }}</td>
                                    <td class="py-2.5">
                                        <x-badge :color="$upcoming->type_color" size="xs">{{ $upcoming->follow_up_type ?? $upcoming->type }}</x-badge>
                                    </td>
                                    <td class="py-2.5 text-charcoal-700 truncate max-w-[200px]">{{ $upcoming->subject ?: $upcoming->follow_up_notes ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>

    {{-- Recent Activities --}}
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-serif text-lg font-semibold text-charcoal-900">Aktivitas Terbaru</h3>
        </div>
        @if($recentActivities->isEmpty())
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-charcoal-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-charcoal-400">Belum ada aktivitas</p>
            </div>
        @else
            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-px bg-charcoal-200"></div>
                <div class="space-y-4">
                    @foreach($recentActivities as $activity)
                        <div class="relative flex gap-4 pl-10">
                            <div class="absolute left-2.5 w-3 h-3 rounded-full bg-{{ $activity->type_color }}-500 ring-4 ring-cream"></div>
                            <div class="flex-1 bg-charcoal-50/50 rounded-xl p-3 hover:bg-charcoal-50 transition-colors">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-badge :color="$activity->type_color" size="xs">{{ config("beauty-crm.activity_types.{$activity->type}", $activity->type) }}</x-badge>
                                    <span class="text-xs text-charcoal-400 font-mono">{{ $activity->activity_date ? $activity->activity_date->format('d M Y H:i') : $activity->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <p class="text-sm font-medium text-charcoal-800">{{ $activity->subject ?: '-' }}</p>
                                @if($activity->description)
                                    <p class="text-xs text-charcoal-500 mt-1 line-clamp-2">{{ $activity->description }}</p>
                                @endif
                                @if($activity->activitable)
                                    <p class="text-xs text-blue-600 mt-1">
                                        {{ class_basename($activity->activitable_type) }}: {{ $activity->activitable->name ?? '-' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-card>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('pipelineChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($pipelineSummary->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($pipelineSummary->pluck('deals_count')) !!},
                        backgroundColor: {!! json_encode($pipelineSummary->pluck('color')) !!},
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: false,
                    cutout: '60%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1A1A1A',
                            titleFont: { family: 'DM Sans', size: 12 },
                            bodyFont: { family: 'DM Mono', size: 11 },
                            padding: 10,
                            cornerRadius: 8,
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
