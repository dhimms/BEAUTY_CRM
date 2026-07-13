@extends('layouts.partials.app')

@section('title', 'My Leads')

@section('breadcrumb')
    <li><a href="{{ route('sales.dashboard') }}" class="hover:text-charcoal-700">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-600 font-medium">Leads</li>
@endsection

@section('page-header', 'My Leads')
@section('page-subtitle', 'Kelola semua lead yang di-assign kepada Anda')

@section('page-actions')
    <a href="{{ route('sales.leads.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Lead
    </a>
@endsection

@section('content')
<div x-data="{ showFilters: true }" class="space-y-6">

    {{-- Filter Bar --}}
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-charcoal-700">Filter & Pencarian</h3>
            <button @click="showFilters = !showFilters" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                <span x-text="showFilters ? 'Sembunyikan' : 'Tampilkan'"></span> Filter
            </button>
        </div>
        <form method="GET" action="{{ route('sales.leads.index') }}" x-show="showFilters" x-transition>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama, email, atau telepon..."
                           class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 transition-all">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 bg-white">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="" {{ request('status') === null ? 'selected' : '' }}>Lead Aktif (Exclude Converted)</option>
                        @foreach(config('beauty-crm.lead_statuses') as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Source --}}
                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Sumber</label>
                    <select name="source" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 bg-white">
                        <option value="">Semua Sumber</option>
                        @foreach($sources as $source)
                            <option value="{{ $source->id }}" {{ request('source') == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Qualification --}}
                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Kualifikasi</label>
                    <select name="qualification" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 bg-white">
                        <option value="">Semua</option>
                        @foreach(config('beauty-crm.lead_qualifications') as $key => $label)
                            <option value="{{ $key }}" {{ request('qualification') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari
                </button>
                <a href="{{ route('sales.leads.index') }}" class="px-4 py-2 text-charcoal-600 text-sm font-medium hover:text-charcoal-800 transition-colors">Reset</a>
            </div>
        </form>
    </x-card>

    {{-- Leads Table --}}
    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="leads-table">
                <thead>
                    <tr class="border-b border-charcoal-200 bg-charcoal-50/50">
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider">Nama</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider hidden sm:table-cell">Kontak</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider hidden md:table-cell">Sumber</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider hidden lg:table-cell">Kualifikasi</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider hidden lg:table-cell">Deals</th>
                        <th class="text-right px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal-100">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('sales.leads.show', $lead) }}" class="text-charcoal-800 font-medium hover:text-blue-600 transition-colors">
                                    {{ $lead->name }}
                                </a>
                                <p class="text-xs text-charcoal-400 mt-0.5">{{ $lead->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <p class="text-charcoal-700 text-xs">{{ $lead->email ?? '-' }}</p>
                                <p class="text-charcoal-500 text-xs">{{ $lead->phone }}</p>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                @if($lead->source)
                                    <span class="text-xs text-charcoal-600">{{ $lead->source->name }}</span>
                                @else
                                    <span class="text-xs text-charcoal-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :color="$lead->status_color" size="xs">{{ config("beauty-crm.lead_statuses.{$lead->status}", $lead->status) }}</x-badge>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                @if($lead->qualification)
                                    <x-badge :color="$lead->qualification_color" size="xs">{{ config("beauty-crm.lead_qualifications.{$lead->qualification}", $lead->qualification) }}</x-badge>
                                @else
                                    <span class="text-xs text-charcoal-400">Belum dinilai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <span class="text-xs font-mono text-charcoal-600">{{ $lead->deals_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button @click="open = !open" class="p-2 text-charcoal-600 bg-white border border-charcoal-200 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-blue-500/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01"/></svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-cloak
                                         class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-lg border border-charcoal-200 overflow-hidden z-20">
                                        <a href="{{ route('sales.leads.show', $lead) }}"
                                           class="flex items-center gap-2 px-4 py-2.5 text-sm text-charcoal-700 hover:bg-charcoal-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Lihat Detail
                                        </a>
                                        @if(!$lead->qualification)
                                            <div class="border-t border-charcoal-100"></div>
                                            <p class="px-4 py-1.5 text-[10px] font-mono text-charcoal-400 uppercase">Qualify As</p>
                                            <form action="{{ route('sales.leads.qualify', $lead) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="qualification" value="qualified">
                                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-emerald-700 hover:bg-emerald-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                                    Qualified
                                                </button>
                                            </form>
                                            <form action="{{ route('sales.leads.qualify', $lead) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="qualification" value="unqualified">
                                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-amber-700 hover:bg-amber-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                                                    Unqualified
                                                </button>
                                            </form>
                                            <form action="{{ route('sales.leads.qualify', $lead) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="qualification" value="not_fit">
                                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-rose-700 hover:bg-rose-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Not Fit
                                                </button>
                                            </form>
                                        @endif
                                        @if($lead->qualification === 'qualified' && $lead->status !== 'converted')
                                            <div class="border-t border-charcoal-100"></div>
                                            <a href="{{ route('sales.deals.create', $lead) }}"
                                               class="flex items-center gap-2 px-4 py-2.5 text-sm text-blue-700 hover:bg-blue-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                                Convert to Deal
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-charcoal-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                <p class="text-charcoal-500 text-sm">Belum ada lead yang di-assign.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
            <div class="px-6 py-4 border-t border-charcoal-100">
                {{ $leads->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
