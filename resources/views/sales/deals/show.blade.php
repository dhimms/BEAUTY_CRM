@extends('layouts.partials.app')

@section('title', 'Deal: ' . $deal->name)

@section('breadcrumb')
    <li><a href="{{ route('sales.dashboard') }}" class="hover:text-charcoal-700">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('sales.deals.index') }}" class="hover:text-charcoal-700">Deals</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-600 font-medium">{{ $deal->name }}</li>
@endsection

@section('page-header', $deal->name)
@section('page-subtitle', 'Detail deal')

@section('page-actions')
    @if($deal->status === 'open')
        {{-- Move to Next Stage --}}
        <button onclick="moveToNextStage()" id="btn-next-stage"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            Next Stage
        </button>

        {{-- Mark Won --}}
        <form action="{{ route('sales.deals.close', $deal) }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="outcome" value="won">
            @php $isClosingStage = strtolower(trim($deal->pipelineStage->name)) === 'closing'; @endphp
            <button type="submit" 
                    @if($isClosingStage) onclick="return confirm('Tandai deal ini sebagai WON? Customer baru akan dibuat.')" @endif
                    @disabled(!$isClosingStage)
                    class="inline-flex items-center gap-2 px-4 py-2 {{ $isClosingStage ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-500 cursor-not-allowed' }} text-sm font-medium rounded-xl transition-colors"
                    @if(!$isClosingStage) title="Deal harus mencapai tahap Closing terlebih dahulu" @endif>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Won
            </button>
        </form>

        {{-- Mark Lost (open modal) --}}
        <button onclick="document.getElementById('lostModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-xl hover:bg-rose-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Lost
        </button>
    @endif
@endsection

@section('content')
<div x-data="{ showEditModal: false, showActivityModal: false }" class="space-y-6">

    {{-- Deal Status Banner --}}
    @if($deal->status !== 'open')
        <div class="rounded-xl p-4 {{ $deal->status === 'won' ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200' }}">
            <div class="flex items-center gap-3">
                @if($deal->status === 'won')
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-emerald-800">Deal Won! 🎉</p>
                        <p class="text-xs text-emerald-600">Closed at {{ $deal->closed_at->format('d M Y H:i') }}</p>
                    </div>
                @else
                    <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-rose-800">Deal Lost</p>
                        <p class="text-xs text-rose-600">
                            Closed at {{ $deal->closed_at->format('d M Y H:i') }}
                            @if($deal->lostReason) — {{ $deal->lostReason->name }} @endif
                        </p>
                        @if($deal->lost_notes)
                            <p class="text-xs text-rose-500 mt-1">{{ $deal->lost_notes }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Stage Progress Bar --}}
    <x-card>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-charcoal-700">Pipeline Progress</h3>
            @if($deal->status === 'open')
                <button @click="showEditModal = true" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Edit Deal</button>
            @endif
        </div>
        <div class="flex items-center gap-1">
            @foreach($stages as $stage)
                @php
                    $isCurrent = $deal->pipeline_stage_id === $stage->id;
                    $isPast = $stage->order < $deal->pipelineStage->order;
                    $isWon = $deal->status === 'won';
                @endphp
                <div class="flex-1 relative group">
                    <div class="h-2 rounded-full transition-all {{ $isCurrent ? 'ring-2 ring-offset-1' : '' }}"
                         style="background-color: {{ ($isPast || $isCurrent || $isWon) ? $stage->color : '#E4E4E4' }};
                                {{ $isCurrent ? 'ring-color: ' . $stage->color : '' }}"></div>
                    <div class="mt-2 text-center">
                        <p class="text-[10px] font-medium {{ $isCurrent ? 'text-charcoal-900' : 'text-charcoal-400' }} truncate">{{ $stage->name }}</p>
                        <p class="text-[9px] font-mono text-charcoal-400">{{ $stage->probability }}%</p>
                    </div>
                    @if($isCurrent)
                        <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-4 h-4 rounded-full border-2 border-white shadow" style="background-color: {{ $stage->color }}"></div>
                    @endif
                </div>
                @if(!$loop->last)
                    <div class="w-2 flex-shrink-0"></div>
                @endif
            @endforeach
        </div>
    </x-card>

    {{-- Deal Info + Lead Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Deal Info --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Informasi Deal</h3>
            <dl class="space-y-3">
                <div class="flex justify-between py-2 border-b border-charcoal-100">
                    <dt class="text-sm text-charcoal-500">Nama Deal</dt>
                    <dd class="text-sm font-medium text-charcoal-800">{{ $deal->name }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-charcoal-100">
                    <dt class="text-sm text-charcoal-500">Value</dt>
                    <dd class="text-sm font-semibold text-emerald-600 font-mono">{{ $deal->formatted_value }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-charcoal-100">
                    <dt class="text-sm text-charcoal-500">Weighted Value</dt>
                    <dd class="text-sm font-medium text-charcoal-600 font-mono">Rp {{ number_format($deal->weighted_value, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-charcoal-100">
                    <dt class="text-sm text-charcoal-500">Stage</dt>
                    <dd>
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium">
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $deal->pipelineStage->color }}"></span>
                            {{ $deal->pipelineStage->name }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between py-2 border-b border-charcoal-100">
                    <dt class="text-sm text-charcoal-500">Status</dt>
                    <dd><x-badge :color="$deal->status_color" size="xs">{{ ucfirst($deal->status) }}</x-badge></dd>
                </div>
                <div class="flex justify-between py-2 border-b border-charcoal-100">
                    <dt class="text-sm text-charcoal-500">Expected Close</dt>
                    <dd class="text-sm font-mono text-charcoal-600">{{ $deal->expected_close_date ? $deal->expected_close_date->format('d M Y') : '-' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-charcoal-100">
                    <dt class="text-sm text-charcoal-500">Assigned To</dt>
                    <dd class="text-sm font-medium text-charcoal-800">{{ $deal->assignedUser->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between py-2">
                    <dt class="text-sm text-charcoal-500">Dibuat</dt>
                    <dd class="text-sm font-mono text-charcoal-600">{{ $deal->created_at->format('d M Y, H:i') }}</dd>
                </div>
            </dl>
        </x-card>

        {{-- Linked Lead --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Lead Terkait</h3>
            @if($deal->lead)
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-serif font-bold text-blue-600">{{ strtoupper(substr($deal->lead->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <a href="{{ route('sales.leads.show', $deal->lead) }}" class="text-sm font-semibold text-charcoal-800 hover:text-blue-600 transition-colors">
                            {{ $deal->lead->name }}
                        </a>
                        <p class="text-xs text-charcoal-500">{{ $deal->lead->email ?? $deal->lead->phone }}</p>
                    </div>
                </div>
                <dl class="space-y-2">
                    <div class="flex justify-between py-1.5 border-b border-charcoal-50">
                        <dt class="text-xs text-charcoal-500">Telepon</dt>
                        <dd class="text-xs font-medium text-charcoal-700">{{ $deal->lead->phone }}</dd>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-charcoal-50">
                        <dt class="text-xs text-charcoal-500">Email</dt>
                        <dd class="text-xs font-medium text-charcoal-700">{{ $deal->lead->email ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-charcoal-50">
                        <dt class="text-xs text-charcoal-500">Sumber</dt>
                        <dd class="text-xs font-medium text-charcoal-700">{{ $deal->lead->source->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <dt class="text-xs text-charcoal-500">Status Lead</dt>
                        <dd><x-badge :color="$deal->lead->status_color" size="xs">{{ config("beauty-crm.lead_statuses.{$deal->lead->status}", $deal->lead->status) }}</x-badge></dd>
                    </div>
                </dl>
            @else
                <p class="text-sm text-charcoal-400 text-center py-4">Lead tidak ditemukan.</p>
            @endif
        </x-card>
    </div>

    {{-- Activity Timeline --}}
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-serif text-lg font-semibold text-charcoal-900">Aktivitas</h3>
            @if($deal->status === 'open')
                <button @click="showActivityModal = true"
                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Log Aktivitas
                </button>
            @endif
        </div>

        @if($deal->activities->isEmpty())
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-charcoal-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-charcoal-400">Belum ada aktivitas tercatat.</p>
            </div>
        @else
            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-px bg-charcoal-200"></div>
                <div class="space-y-4">
                    @foreach($deal->activities as $activity)
                        <div class="relative flex gap-4 pl-10" x-data="{ expanded: false }">
                            <div class="absolute left-2.5 w-3 h-3 rounded-full bg-{{ $activity->type_color }}-500 ring-4 ring-white"></div>
                            <div class="flex-1 bg-charcoal-50/50 rounded-xl p-4 hover:bg-charcoal-50 transition-colors group">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <x-badge :color="$activity->type_color" size="xs">{{ config("beauty-crm.activity_types.{$activity->type}", $activity->type) }}</x-badge>
                                            @if($activity->result)
                                                <span class="text-xs text-charcoal-400">• {{ $activity->result }}</span>
                                            @endif
                                            @if($activity->duration)
                                                <span class="text-xs text-charcoal-400">• {{ $activity->duration }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm font-medium text-charcoal-800">{{ $activity->subject ?: 'No subject' }}</p>
                                        @if($activity->description)
                                            <p class="text-xs text-charcoal-500 mt-1" :class="expanded ? '' : 'line-clamp-2'">{{ $activity->description }}</p>
                                            @if(strlen($activity->description) > 120)
                                                <button @click="expanded = !expanded" class="text-xs text-blue-600 hover:text-blue-700 mt-1" x-text="expanded ? 'Show less' : 'Show more'"></button>
                                            @endif
                                        @endif
                                        @if($activity->follow_up_date)
                                            <div class="mt-2 flex items-center gap-2">
                                                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg {{ $activity->follow_up_status === 'done' ? 'bg-emerald-50 text-emerald-700' : ($activity->follow_up_date->isPast() ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    Follow-up: {{ $activity->follow_up_date->format('d M Y') }}
                                                </span>
                                                @if($activity->follow_up_status === 'pending')
                                                    <form action="{{ route('sales.activities.complete-followup', $activity) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">✓ Done</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                                        <span class="text-xs font-mono text-charcoal-400">{{ $activity->activity_date ? $activity->activity_date->format('d M H:i') : $activity->created_at->format('d M H:i') }}</span>
                                        <form action="{{ route('sales.activities.destroy', $activity) }}" method="POST"
                                              onsubmit="return confirm('Hapus aktivitas ini?')" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-charcoal-400 hover:text-rose-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if($activity->user)
                                    <div class="flex items-center gap-2 mt-2 pt-2 border-t border-charcoal-100">
                                        <img src="{{ $activity->user->avatar_url }}" class="w-5 h-5 rounded-full" alt="">
                                        <span class="text-xs text-charcoal-400">{{ $activity->user->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-card>

    {{-- Lost Modal --}}
    <div id="lostModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/40" onclick="document.getElementById('lostModal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-charcoal-100 flex items-center justify-between">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Tandai Deal sebagai Lost</h3>
                <button onclick="document.getElementById('lostModal').classList.add('hidden')" class="text-charcoal-400 hover:text-charcoal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('sales.deals.close', $deal) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="outcome" value="lost">

                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Alasan Lost *</label>
                    <select name="lost_reason_id" required class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-300 bg-white">
                        <option value="">Pilih alasan...</option>
                        @foreach($lostReasons as $reason)
                            <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Catatan Lost *</label>
                    <textarea name="lost_notes" rows="3" required class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-300 resize-none" placeholder="Jelaskan mengapa deal ini lost..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('lostModal').classList.add('hidden')" class="px-4 py-2 text-sm text-charcoal-600 hover:text-charcoal-800">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-rose-600 text-white text-sm font-medium rounded-xl hover:bg-rose-700 transition-colors">Tandai Lost</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Deal Modal --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/40" @click="showEditModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" @click.stop>
            <div class="px-6 py-4 border-b border-charcoal-100 flex items-center justify-between">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Edit Deal</h3>
                <button @click="showEditModal = false" class="text-charcoal-400 hover:text-charcoal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('sales.deals.update', $deal) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Nama Deal *</label>
                    <input type="text" name="name" value="{{ $deal->name }}" required class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300">
                </div>

                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Value (Rp) *</label>
                    <input type="number" name="value" value="{{ $deal->value }}" required min="0" step="1000" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300">
                </div>

                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Expected Close Date</label>
                    <input type="date" name="expected_close_date" value="{{ $deal->expected_close_date ? $deal->expected_close_date->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm text-charcoal-600 hover:text-charcoal-800">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Activity Modal --}}
    <div x-show="showActivityModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/40" @click="showActivityModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="px-6 py-4 border-b border-charcoal-100 flex items-center justify-between">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Log Aktivitas Baru</h3>
                <button @click="showActivityModal = false" class="text-charcoal-400 hover:text-charcoal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('sales.activities.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="activitable_type" value="deal">
                <input type="hidden" name="activitable_id" value="{{ $deal->id }}">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Tipe *</label>
                        <select name="type" required class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 bg-white">
                            @foreach(config('beauty-crm.activity_types') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Durasi</label>
                        <select name="duration" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 bg-white">
                            <option value="">-</option>
                            <option value="5min">5 menit</option>
                            <option value="15min">15 menit</option>
                            <option value="30min">30 menit</option>
                            <option value="1hr">1 jam</option>
                            <option value="2hr">2 jam</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Subject</label>
                    <input type="text" name="subject" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300" placeholder="Ringkasan aktivitas...">
                </div>

                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 resize-none" placeholder="Detail aktivitas..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Tanggal Aktivitas</label>
                        <input type="datetime-local" name="activity_date" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300">
                    </div>
                    <div>
                        <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Hasil</label>
                        <select name="result" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 bg-white">
                            <option value="">-</option>
                            @foreach(config('beauty-crm.activity_results') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="border-t border-charcoal-100 pt-4">
                    <p class="text-xs font-mono text-charcoal-400 uppercase mb-3">Jadwalkan Follow-up</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-charcoal-500 mb-1">Tanggal</label>
                            <input type="date" name="follow_up_date" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300">
                        </div>
                        <div>
                            <label class="block text-xs text-charcoal-500 mb-1">Tipe</label>
                            <select name="follow_up_type" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 bg-white">
                                <option value="">-</option>
                                <option value="call">Telepon</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="email">Email</option>
                                <option value="meeting">Meeting</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-xs text-charcoal-500 mb-1">Catatan Follow-up</label>
                        <input type="text" name="follow_up_notes" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300" placeholder="Catatan untuk follow-up...">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showActivityModal = false" class="px-4 py-2 text-sm text-charcoal-600 hover:text-charcoal-800 transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">Simpan Aktivitas</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function moveToNextStage() {
    const btn = document.getElementById('btn-next-stage');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Moving...';

    fetch(`/sales/deals/{{ $deal->id }}/move-stage`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg> Next Stage';
        } else {
            window.location.reload();
        }
    })
    .catch(() => {
        alert('Gagal memindahkan stage.');
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg> Next Stage';
    });
}
</script>
@endpush
@endsection
