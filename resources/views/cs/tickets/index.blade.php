@extends('layouts.partials.app')

@section('title', 'Service Tickets')

@section('breadcrumb')
    <li><a href="{{ route('cs.dashboard') }}" class="hover:text-emerald-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Tickets</li>
@endsection

@section('page-header', 'Service Tickets')
@section('page-subtitle', 'Kelola semua service ticket customer')

@section('page-actions')
    <a href="{{ route('cs.tickets.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Ticket
    </a>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Filters --}}
    <div class="p-4 border-b border-charcoal-100">
        <form method="GET" action="{{ route('cs.tickets.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[180px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ticket..."
                    class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
            </div>
            <select name="status" class="px-3 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Status</option>
                @foreach(config('beauty-crm.ticket_statuses') as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="priority" class="px-3 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Priority</option>
                @foreach(config('beauty-crm.ticket_priorities') as $key => $label)
                    <option value="{{ $key }}" {{ request('priority') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="category" class="px-3 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Kategori</option>
                @foreach(config('beauty-crm.ticket_categories') as $key => $label)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2.5 bg-charcoal-800 text-white rounded-xl text-sm font-medium hover:bg-charcoal-900 transition-colors">Filter</button>
            @if(request()->hasAny(['search', 'status', 'priority', 'category', 'assigned_to']))
                <a href="{{ route('cs.tickets.index') }}" class="text-charcoal-500 hover:text-charcoal-700 text-sm">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">No. Ticket</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">CS</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100" id="tickets-table">
                @forelse($tickets as $ticket)
                    <tr class="hover:bg-charcoal-50/30 transition-colors" id="ticket-row-{{ $ticket->id }}">
                        <td class="px-6 py-4">
                            <a href="{{ route('cs.tickets.show', $ticket) }}" class="font-mono text-emerald-600 hover:text-emerald-700 font-medium">{{ $ticket->ticket_number }}</a>
                        </td>
                        <td class="px-6 py-4 text-charcoal-700">{{ $ticket->customer?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-charcoal-900 font-medium truncate max-w-[180px]">{{ $ticket->title }}</td>
                        <td class="px-6 py-4 text-charcoal-600 text-xs">{{ config('beauty-crm.ticket_categories.' . $ticket->category, '-') }}</td>
                        <td class="px-6 py-4"><x-badge :color="$ticket->priority_color" size="xs">{{ ucfirst($ticket->priority) }}</x-badge></td>
                        <td class="px-6 py-4" x-data="{ status: '{{ $ticket->status }}' }">
                            <div class="flex items-center gap-2">
                                <x-badge :color="$ticket->status_color" size="xs" id="status-badge-{{ $ticket->id }}">
                                    {{ config('beauty-crm.ticket_statuses.' . $ticket->status) }}
                                </x-badge>
                                @if($ticket->status !== 'closed')
                                    <button onclick="advanceStatus({{ $ticket->id }}, '{{ $ticket->status }}')"
                                        class="text-emerald-600 hover:text-emerald-700" title="Advance status">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-charcoal-600 text-xs">{{ $ticket->assignedUser?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('cs.tickets.edit', $ticket) }}" class="text-charcoal-500 hover:text-charcoal-700" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('cs.tickets.destroy', $ticket) }}" class="inline" onsubmit="return confirm('Hapus ticket ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:text-rose-600" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-charcoal-400">
                            <p>Belum ada ticket.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tickets->hasPages())
        <div class="p-4 border-t border-charcoal-100">{{ $tickets->links() }}</div>
    @endif
</x-card>
@endsection

@push('scripts')
<script>
function advanceStatus(ticketId, currentStatus) {
    const nextStatus = {
        'open': 'in_progress',
        'in_progress': 'resolved',
        'resolved': 'closed'
    };

    const next = nextStatus[currentStatus];
    if (!next) return;

    if (!confirm(`Ubah status ke "${next.replace('_', ' ')}"?`)) return;

    fetch(`/cs/tickets/${ticketId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ status: next })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Gagal mengubah status.');
        }
    })
    .catch(() => alert('Terjadi kesalahan.'));
}
</script>
@endpush
