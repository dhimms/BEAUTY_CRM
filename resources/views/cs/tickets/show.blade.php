@extends('layouts.partials.app')

@section('title', 'Ticket ' . $ticket->ticket_number)

@section('breadcrumb')
    <li><a href="{{ route('cs.dashboard') }}" class="hover:text-emerald-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('cs.tickets.index') }}" class="hover:text-emerald-600">Tickets</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">{{ $ticket->ticket_number }}</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Ticket Detail --}}
    <div class="lg:col-span-2 space-y-6">
        <x-card>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="font-mono text-sm text-charcoal-400 mb-1">{{ $ticket->ticket_number }}</p>
                    <h1 class="font-serif text-2xl font-semibold text-charcoal-900">{{ $ticket->title }}</h1>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('cs.tickets.edit', $ticket) }}" class="px-3 py-1.5 border border-charcoal-200 text-charcoal-700 rounded-lg text-xs font-medium hover:bg-charcoal-50">Edit</a>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 mb-6">
                <x-badge :color="$ticket->status_color" size="sm" id="detail-status">{{ config('beauty-crm.ticket_statuses.' . $ticket->status) }}</x-badge>
                <x-badge :color="$ticket->priority_color" size="sm">{{ ucfirst($ticket->priority) }}</x-badge>
                @if($ticket->category)
                    <x-badge color="purple" size="sm">{{ config('beauty-crm.ticket_categories.' . $ticket->category) }}</x-badge>
                @endif
            </div>

            {{-- Status Actions --}}
            @if($ticket->status !== 'closed')
                <div class="flex items-center gap-2 mb-6 p-3 bg-charcoal-50 rounded-xl">
                    <span class="text-sm text-charcoal-600">Ubah status:</span>
                    @php
                        $transitions = match($ticket->status) {
                            'open' => ['in_progress' => 'In Progress'],
                            'in_progress' => ['resolved' => 'Resolved', 'open' => 'Reopen'],
                            'resolved' => ['closed' => 'Close', 'in_progress' => 'Re-process'],
                            default => [],
                        };
                    @endphp
                    @foreach($transitions as $newStatus => $label)
                        <button onclick="updateStatus('{{ $newStatus }}')"
                            class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-medium hover:bg-emerald-700 transition-colors">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            @endif

            @if($ticket->description)
                <div class="prose prose-sm max-w-none text-charcoal-700">
                    <h4 class="text-sm font-medium text-charcoal-500 mb-2">Deskripsi</h4>
                    <p>{{ $ticket->description }}</p>
                </div>
            @endif
        </x-card>

        {{-- Activity Timeline --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Timeline Aktivitas</h3>
            @if($ticket->activities->count() > 0)
                <div class="relative">
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-charcoal-200"></div>
                    <div class="space-y-4">
                        @foreach($ticket->activities as $activity)
                            <div class="relative flex gap-4 pl-2">
                                <div class="w-10 h-10 rounded-full bg-{{ $activity->type_color }}-100 text-{{ $activity->type_color }}-600 flex items-center justify-center flex-shrink-0 z-10 border-2 border-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 bg-charcoal-50/50 rounded-xl p-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-semibold text-charcoal-900">{{ $activity->subject ?? ucfirst($activity->type) }}</span>
                                        <span class="text-xs text-charcoal-400">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($activity->description)
                                        <p class="text-sm text-charcoal-600">{{ $activity->description }}</p>
                                    @endif
                                    <p class="text-xs text-charcoal-400 mt-1">oleh {{ $activity->user?->name ?? '-' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-charcoal-400 text-sm text-center py-6">Belum ada aktivitas.</p>
            @endif
        </x-card>
    </div>

    {{-- Sidebar Info --}}
    <div class="space-y-6">
        <x-card>
            <h4 class="text-xs font-mono text-charcoal-400 uppercase tracking-wider mb-4">Detail Ticket</h4>
            <div class="space-y-4">
                <div>
                    <label class="text-xs text-charcoal-400">Customer</label>
                    <p class="text-sm font-medium text-charcoal-900">
                        <a href="{{ route('cs.customers.show', $ticket->customer) }}" class="text-emerald-600 hover:text-emerald-700">{{ $ticket->customer?->name }}</a>
                    </p>
                </div>
                <div>
                    <label class="text-xs text-charcoal-400">Assigned to</label>
                    <p class="text-sm text-charcoal-900">{{ $ticket->assignedUser?->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs text-charcoal-400">Dibuat</label>
                    <p class="text-sm text-charcoal-900">{{ $ticket->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <label class="text-xs text-charcoal-400">Terakhir Update</label>
                    <p class="text-sm text-charcoal-900">{{ $ticket->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </x-card>

        {{-- Log Activity for Ticket --}}
        <x-card>
            <h4 class="text-xs font-mono text-charcoal-400 uppercase tracking-wider mb-4">Log Aktivitas</h4>
            <form method="POST" action="{{ route('cs.activities.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="activitable_type" value="ticket">
                <input type="hidden" name="activitable_id" value="{{ $ticket->id }}">
                <select name="type" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                    @foreach(config('beauty-crm.activity_types') as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <input type="text" name="subject" placeholder="Subject" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <textarea name="description" rows="2" placeholder="Deskripsi..." class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors">Simpan Aktivitas</button>
            </form>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(newStatus) {
    if (!confirm(`Ubah status ke "${newStatus.replace('_', ' ')}"?`)) return;

    fetch(`/cs/tickets/{{ $ticket->id }}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { location.reload(); }
        else { alert(data.message); }
    })
    .catch(() => alert('Terjadi kesalahan.'));
}
</script>
@endpush
