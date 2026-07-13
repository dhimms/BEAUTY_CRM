@extends('layouts.partials.app')

@section('title', 'CS Dashboard')

@section('page-header', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name . ' — Customer Service Panel')

@section('content')
{{-- KPI Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <x-kpi-card label="Total Customers" :value="$totalCustomers" color="emerald"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>' />

    <x-kpi-card label="Active Customers" :value="$activeCustomers" color="blue"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>' />

    <x-kpi-card label="Open Tickets" :value="$openTickets" color="amber"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>' />

    <x-kpi-card label="Follow-ups Today" :value="$todayFollowUps->count()" color="rose"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>' />
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Today's Follow-ups --}}
    <div class="lg:col-span-2">
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Follow-up Hari Ini</h3>
                <a href="{{ route('cs.follow-ups.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">Lihat Semua →</a>
            </div>

            @if($todayFollowUps->count() > 0)
                <div class="space-y-3">
                    @foreach($todayFollowUps as $fu)
                        <div class="flex items-center gap-4 p-3 rounded-xl bg-emerald-50/50 border border-emerald-100">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-charcoal-900 truncate">
                                    {{ $fu->activitable?->name ?? 'Customer' }}
                                </p>
                                <p class="text-xs text-charcoal-500">{{ $fu->follow_up_notes ?? $fu->subject ?? 'Follow-up' }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <x-badge color="emerald" size="xs">{{ ucfirst($fu->follow_up_type ?? 'call') }}</x-badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-charcoal-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-charcoal-500 text-sm">Tidak ada follow-up hari ini 🎉</p>
                </div>
            @endif
        </x-card>
    </div>

    {{-- Ticket by Priority Chart --}}
    <div>
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Ticket by Priority</h3>
            <div class="flex items-center justify-center" style="height: 220px;">
                <canvas id="priorityChart"></canvas>
            </div>
            <div class="mt-4 text-center">
                <p class="text-xs text-charcoal-500">
                    <span class="font-semibold text-charcoal-700">{{ $newCustomersThisMonth }}</span> customer baru bulan ini
                </p>
            </div>
        </x-card>
    </div>
</div>

{{-- Recent Tickets --}}
<x-card :padding="false">
    <div class="p-6 border-b border-charcoal-100">
        <div class="flex items-center justify-between">
            <h3 class="font-serif text-lg font-semibold text-charcoal-900">Ticket Terbaru</h3>
            <a href="{{ route('cs.tickets.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">Lihat Semua →</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">No. Ticket</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($recentTickets as $ticket)
                    <tr class="hover:bg-charcoal-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('cs.tickets.show', $ticket) }}" class="font-mono text-emerald-600 hover:text-emerald-700 font-medium">
                                {{ $ticket->ticket_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-charcoal-700">{{ $ticket->customer?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-charcoal-900 font-medium truncate max-w-[200px]">{{ $ticket->title }}</td>
                        <td class="px-6 py-4"><x-badge :color="$ticket->priority_color" size="xs">{{ ucfirst($ticket->priority) }}</x-badge></td>
                        <td class="px-6 py-4"><x-badge :color="$ticket->status_color" size="xs">{{ config('beauty-crm.ticket_statuses.' . $ticket->status, $ticket->status) }}</x-badge></td>
                        <td class="px-6 py-4 text-charcoal-500 text-xs">{{ $ticket->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-charcoal-400">Belum ada ticket.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

{{-- Overdue Follow-ups Warning --}}
@if($overdueFollowUps->count() > 0)
<div class="mt-6">
    <x-card>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="font-serif text-lg font-semibold text-rose-700">Overdue Follow-ups ({{ $overdueFollowUps->count() }})</h3>
        </div>
        <div class="space-y-2">
            @foreach($overdueFollowUps->take(5) as $overdue)
                <div class="flex items-center justify-between p-3 rounded-lg bg-rose-50 border border-rose-100">
                    <div>
                        <p class="text-sm font-medium text-charcoal-900">{{ $overdue->activitable?->name ?? 'Customer' }}</p>
                        <p class="text-xs text-rose-600">Jatuh tempo: {{ $overdue->follow_up_date->format('d M Y') }}</p>
                    </div>
                    <x-badge color="red" size="xs">Overdue</x-badge>
                </div>
            @endforeach
        </div>
    </x-card>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const priorityData = @json($ticketsByPriority);
    const labels = Object.keys(priorityData).map(k => k.charAt(0).toUpperCase() + k.slice(1));
    const values = Object.values(priorityData);
    const colors = {
        'low': '#9CA3AF',
        'medium': '#F59E0B',
        'high': '#EF4444',
        'urgent': '#E11D48'
    };
    const bgColors = Object.keys(priorityData).map(k => colors[k] || '#6B7280');

    if (document.getElementById('priorityChart')) {
        new Chart(document.getElementById('priorityChart'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: bgColors,
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            usePointStyle: true,
                            pointStyleWidth: 10,
                            font: { size: 11, family: 'DM Sans' }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
