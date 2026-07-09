@extends('layouts.partials.app')

@section('title', 'Pipeline')

@section('breadcrumb')
    <li><a href="{{ route('sales.dashboard') }}" class="hover:text-charcoal-700">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-600 font-medium">Pipeline</li>
@endsection

@section('page-header', 'Pipeline Board')
@section('page-subtitle', 'Drag & drop deals antar stage')

@section('page-actions')
    <a href="{{ route('sales.deals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-charcoal-200 text-charcoal-700 text-sm font-medium rounded-xl hover:bg-charcoal-50 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
        List View
    </a>
@endsection

@section('content')
<div id="pipeline-board" class="flex gap-4 overflow-x-auto pb-4" style="min-height: 70vh;">
    @foreach($stages as $stage)
        @php
            $stageDeals = $stage->deals;
            $totalValue = $stageDeals->sum('value');
        @endphp
        <div class="flex-shrink-0 w-72 lg:w-80 flex flex-col">
            {{-- Column Header --}}
            <div class="bg-white rounded-t-xl border border-charcoal-200 border-b-0 px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $stage->color }}"></div>
                        <h3 class="text-sm font-semibold text-charcoal-800">{{ $stage->name }}</h3>
                        <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-charcoal-500 bg-charcoal-100 rounded-full">{{ $stageDeals->count() }}</span>
                    </div>
                    <span class="text-xs font-mono text-charcoal-400">{{ $stage->probability }}%</span>
                </div>
            </div>

            {{-- Droppable Area --}}
            <div class="kanban-column flex-1 bg-charcoal-50/50 border border-charcoal-200 border-t-0 rounded-b-xl p-2 space-y-2 overflow-y-auto"
                 data-stage-id="{{ $stage->id }}"
                 style="border-top: 3px solid {{ $stage->color }}; min-height: 200px;">

                @foreach($stageDeals as $deal)
                    <div class="kanban-card bg-white rounded-xl border border-charcoal-200 p-3 cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow group"
                         data-deal-id="{{ $deal->id }}"
                         style="border-left: 3px solid {{ $stage->color }}">
                        {{-- Deal Name --}}
                        <a href="{{ route('sales.deals.show', $deal) }}" class="text-sm font-medium text-charcoal-800 hover:text-blue-600 transition-colors block truncate">
                            {{ $deal->name }}
                        </a>

                        {{-- Lead Name --}}
                        <p class="text-xs text-charcoal-500 mt-1 truncate">
                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $deal->lead->name ?? '-' }}
                        </p>

                        {{-- Value + Close Date --}}
                        <div class="flex items-center justify-between mt-3">
                            <span class="text-xs font-semibold text-emerald-600 font-mono">{{ $deal->formatted_value }}</span>
                            @if($deal->expected_close_date)
                                <span class="text-[10px] text-charcoal-400 font-mono flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $deal->expected_close_date->format('d M') }}
                                </span>
                            @endif
                        </div>

                        {{-- Sales Avatar --}}
                        @if($deal->assignedUser)
                            <div class="flex items-center gap-2 mt-2 pt-2 border-t border-charcoal-100">
                                <img src="{{ $deal->assignedUser->avatar_url }}" class="w-5 h-5 rounded-full" alt="{{ $deal->assignedUser->name }}">
                                <span class="text-[10px] text-charcoal-400 truncate">{{ $deal->assignedUser->name }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach

                @if($stageDeals->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-xs text-charcoal-400">Tidak ada deal</p>
                    </div>
                @endif
            </div>

            {{-- Column Footer --}}
            <div class="bg-white rounded-b-xl border border-charcoal-200 border-t-0 px-4 py-2 mt-0 -mt-px">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono text-charcoal-400 uppercase">Total Value</span>
                    <span class="text-xs font-semibold text-charcoal-700 font-mono">Rp {{ number_format($totalValue, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const columns = document.querySelectorAll('.kanban-column');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    columns.forEach(function(column) {
        new Sortable(column, {
            group: 'pipeline',
            animation: 200,
            ghostClass: 'opacity-40',
            chosenClass: 'ring-2 ring-blue-400',
            dragClass: 'shadow-xl',
            draggable: '.kanban-card',
            easing: 'cubic-bezier(0.22, 1, 0.36, 1)',

            onEnd: function(evt) {
                const dealId = evt.item.dataset.dealId;
                const newStageId = evt.to.dataset.stageId;
                const oldStageId = evt.from.dataset.stageId;

                if (newStageId === oldStageId) return;

                // Update card left border color
                const stageColor = evt.to.style.borderTopColor;
                evt.item.style.borderLeftColor = stageColor;

                // Send AJAX request
                fetch(`/sales/deals/${dealId}/move-stage`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ stage_id: newStageId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        // Revert on error
                        evt.from.appendChild(evt.item);
                        showToast(data.error, 'error');
                    } else {
                        showToast(data.message, 'success');
                        updateColumnCounts();
                    }
                })
                .catch(err => {
                    evt.from.appendChild(evt.item);
                    showToast('Gagal memindahkan deal.', 'error');
                });
            }
        });
    });

    function updateColumnCounts() {
        columns.forEach(function(column) {
            const cards = column.querySelectorAll('.kanban-card');
            const header = column.previousElementSibling;
            if (header) {
                const countBadge = header.querySelector('.rounded-full');
                if (countBadge) countBadge.textContent = cards.length;
            }
        });
    }

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 z-50 px-4 py-3 rounded-xl shadow-lg text-sm font-medium transition-all transform translate-y-0 ${
            type === 'success' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
</script>
@endpush

@push('styles')
<style>
    .kanban-column {
        scrollbar-width: thin;
        scrollbar-color: #D1D1D1 transparent;
    }
    .kanban-column::-webkit-scrollbar { width: 4px; }
    .kanban-column::-webkit-scrollbar-track { background: transparent; }
    .kanban-column::-webkit-scrollbar-thumb { background: #D1D1D1; border-radius: 2px; }
</style>
@endpush
@endsection
