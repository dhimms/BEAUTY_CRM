@extends('layouts.partials.app')

@section('title', 'Lead: ' . $lead->name)

@section('breadcrumb')
    <li><a href="{{ route('sales.dashboard') }}" class="hover:text-charcoal-700">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('sales.leads.index') }}" class="hover:text-charcoal-700">Leads</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-600 font-medium">{{ $lead->name }}</li>
@endsection

@section('page-header', $lead->name)
@section('page-subtitle', 'Detail informasi lead')

@section('page-actions')
    {{-- Qualify Dropdown --}}
    @if(!$lead->qualification)
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Qualify Lead
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" @click.outside="open = false" x-cloak
                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-charcoal-200 overflow-hidden z-20">
                <form action="{{ route('sales.leads.qualify', $lead) }}" method="POST">
                    @csrf
                    <input type="hidden" name="qualification" value="qualified">
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-emerald-700 hover:bg-emerald-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                        Qualified
                    </button>
                </form>
                <form action="{{ route('sales.leads.qualify', $lead) }}" method="POST">
                    @csrf
                    <input type="hidden" name="qualification" value="unqualified">
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-amber-700 hover:bg-amber-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                        Unqualified
                    </button>
                </form>
                <form action="{{ route('sales.leads.qualify', $lead) }}" method="POST">
                    @csrf
                    <input type="hidden" name="qualification" value="not_fit">
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-rose-700 hover:bg-rose-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Not Fit
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Convert to Deal --}}
    @if($lead->qualification === 'qualified' && $lead->status !== 'converted')
        <a href="{{ route('sales.deals.create', $lead) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            Convert to Deal
        </a>
    @endif
@endsection

@section('content')
<div x-data="{ activeTab: 'overview', showActivityModal: false }" class="space-y-6">

    {{-- Lead Info Header Card --}}
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <span class="text-xl font-serif font-bold text-blue-600">{{ strtoupper(substr($lead->name, 0, 1)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="text-lg font-serif font-semibold text-charcoal-900">{{ $lead->name }}</h2>
                    <x-badge :color="$lead->status_color" size="sm">{{ config("beauty-crm.lead_statuses.{$lead->status}", $lead->status) }}</x-badge>
                    @if($lead->qualification)
                        <x-badge :color="$lead->qualification_color" size="sm">{{ config("beauty-crm.lead_qualifications.{$lead->qualification}", $lead->qualification) }}</x-badge>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-4 text-sm text-charcoal-500">
                    @if($lead->email)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $lead->email }}
                        </span>
                    @endif
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $lead->phone }}
                    </span>
                    @if($lead->source)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/></svg>
                            {{ $lead->source->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </x-card>

    {{-- Tabs --}}
    <div class="border-b border-charcoal-200">
        <nav class="flex gap-0 overflow-x-auto">
            @foreach(['overview' => 'Overview', 'activities' => 'Activities', 'notes' => 'Notes', 'deals' => 'Deals'] as $tab => $label)
                <button @click="activeTab = '{{ $tab }}'"
                        :class="activeTab === '{{ $tab }}'
                            ? 'border-blue-500 text-blue-600'
                            : 'border-transparent text-charcoal-500 hover:text-charcoal-700 hover:border-charcoal-300'"
                        class="px-5 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                    {{ $label }}
                    @if($tab === 'activities')
                        <span class="ml-1.5 inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full bg-charcoal-100 text-charcoal-600">{{ $lead->activities->count() }}</span>
                    @elseif($tab === 'deals')
                        <span class="ml-1.5 inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full bg-charcoal-100 text-charcoal-600">{{ $lead->deals->count() }}</span>
                    @endif
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Tab: Overview --}}
    <div x-show="activeTab === 'overview'" x-transition>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Contact Info --}}
            <x-card>
                <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Informasi Kontak</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-charcoal-100">
                        <dt class="text-sm text-charcoal-500">Nama</dt>
                        <dd class="text-sm font-medium text-charcoal-800">{{ $lead->name }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-charcoal-100">
                        <dt class="text-sm text-charcoal-500">Email</dt>
                        <dd class="text-sm font-medium text-charcoal-800">{{ $lead->email ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-charcoal-100">
                        <dt class="text-sm text-charcoal-500">Telepon</dt>
                        <dd class="text-sm font-medium text-charcoal-800">{{ $lead->phone }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-charcoal-100">
                        <dt class="text-sm text-charcoal-500">Alamat</dt>
                        <dd class="text-sm font-medium text-charcoal-800 text-right max-w-[60%]">{{ $lead->address ?? '-' }}</dd>
                    </div>
                </dl>
            </x-card>

            {{-- Lead Details --}}
            <x-card>
                <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Detail Lead</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-charcoal-100">
                        <dt class="text-sm text-charcoal-500">Sumber</dt>
                        <dd class="text-sm font-medium text-charcoal-800">{{ $lead->source->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-charcoal-100">
                        <dt class="text-sm text-charcoal-500">Status</dt>
                        <dd><x-badge :color="$lead->status_color" size="xs">{{ config("beauty-crm.lead_statuses.{$lead->status}", $lead->status) }}</x-badge></dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-charcoal-100">
                        <dt class="text-sm text-charcoal-500">Kualifikasi</dt>
                        <dd>
                            @if($lead->qualification)
                                <x-badge :color="$lead->qualification_color" size="xs">{{ config("beauty-crm.lead_qualifications.{$lead->qualification}", $lead->qualification) }}</x-badge>
                            @else
                                <span class="text-sm text-charcoal-400">Belum dinilai</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-charcoal-100">
                        <dt class="text-sm text-charcoal-500">Assigned To</dt>
                        <dd class="text-sm font-medium text-charcoal-800">{{ $lead->assignedUser->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-charcoal-100">
                        <dt class="text-sm text-charcoal-500">Dibuat oleh</dt>
                        <dd class="text-sm font-medium text-charcoal-800">{{ $lead->creator->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-charcoal-500">Tanggal Masuk</dt>
                        <dd class="text-sm font-mono text-charcoal-600">{{ $lead->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>

    {{-- Tab: Activities --}}
    <div x-show="activeTab === 'activities'" x-transition>
        <div class="space-y-4">
            {{-- Add Activity Button --}}
            <div class="flex justify-end">
                <button @click="showActivityModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Log Aktivitas
                </button>
            </div>

            {{-- Activity Timeline --}}
            <x-card>
                @if($lead->activities->isEmpty())
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-charcoal-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-charcoal-400">Belum ada aktivitas tercatat.</p>
                        <button @click="showActivityModal = true" class="mt-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
                            Tambah aktivitas pertama →
                        </button>
                    </div>
                @else
                    <div class="relative">
                        <div class="absolute left-4 top-0 bottom-0 w-px bg-charcoal-200"></div>
                        <div class="space-y-4">
                            @foreach($lead->activities as $activity)
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
                                                            @if($activity->follow_up_type) ({{ $activity->follow_up_type }}) @endif
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
                                                <span class="text-xs font-mono text-charcoal-400">{{ $activity->activity_date ? $activity->activity_date->format('d M Y H:i') : $activity->created_at->format('d M Y H:i') }}</span>
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
        </div>
    </div>

    {{-- Tab: Notes --}}
    <div x-show="activeTab === 'notes'" x-transition>
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Catatan Internal</h3>
            @if($lead->notes)
                <div class="prose prose-sm max-w-none text-charcoal-700">
                    {!! nl2br(e($lead->notes)) !!}
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-charcoal-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm text-charcoal-400">Belum ada catatan untuk lead ini.</p>
                </div>
            @endif
        </x-card>
    </div>

    {{-- Tab: Deals --}}
    <div x-show="activeTab === 'deals'" x-transition>
        <div class="space-y-4">
            @if($lead->qualification === 'qualified' && $lead->status !== 'converted')
                <div class="flex justify-end">
                    <a href="{{ route('sales.deals.create', $lead) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Deal Baru
                    </a>
                </div>
            @endif

            <x-card :padding="false">
                @if($lead->deals->isEmpty())
                    <div class="text-center py-8 px-6">
                        <svg class="w-12 h-12 text-charcoal-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                        </svg>
                        <p class="text-sm text-charcoal-400">Belum ada deal terkait lead ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-charcoal-200 bg-charcoal-50/50">
                                    <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase">Deal</th>
                                    <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase">Value</th>
                                    <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase">Stage</th>
                                    <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase">Status</th>
                                    <th class="text-right px-6 py-3 text-xs font-mono text-charcoal-400 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-charcoal-100">
                                @foreach($lead->deals as $deal)
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-6 py-3">
                                            <a href="{{ route('sales.deals.show', $deal) }}" class="text-charcoal-800 font-medium hover:text-blue-600 transition-colors">{{ $deal->name }}</a>
                                        </td>
                                        <td class="px-6 py-3 font-mono text-charcoal-700">{{ $deal->formatted_value }}</td>
                                        <td class="px-6 py-3">
                                            @if($deal->pipelineStage)
                                                <span class="inline-flex items-center gap-1.5 text-xs">
                                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $deal->pipelineStage->color }}"></span>
                                                    {{ $deal->pipelineStage->name }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3">
                                            <x-badge :color="$deal->status_color" size="xs">{{ ucfirst($deal->status) }}</x-badge>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('sales.deals.show', $deal) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat →</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
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
                <input type="hidden" name="activitable_type" value="lead">
                <input type="hidden" name="activitable_id" value="{{ $lead->id }}">

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

                {{-- Follow-up Section --}}
                <div class="border-t border-charcoal-100 pt-4">
                    <p class="text-xs font-mono text-charcoal-400 uppercase mb-3">Jadwalkan Follow-up</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-charcoal-500 mb-1">Tanggal Follow-up</label>
                            <input type="date" name="follow_up_date" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300">
                        </div>
                        <div>
                            <label class="block text-xs text-charcoal-500 mb-1">Tipe Follow-up</label>
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
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
                        Simpan Aktivitas
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
