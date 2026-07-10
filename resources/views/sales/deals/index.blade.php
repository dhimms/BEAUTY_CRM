@extends('layouts.partials.app')

@section('title', 'My Deals')

@section('breadcrumb')
    <li><a href="{{ route('sales.dashboard') }}" class="hover:text-charcoal-700">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-600 font-medium">Deals</li>
@endsection

@section('page-header', 'My Deals')
@section('page-subtitle', 'Daftar semua deal Anda')

@section('page-actions')
    <a href="{{ route('sales.deals.pipeline') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
        Pipeline View
    </a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Filter Bar --}}
    <x-card>
        <form method="GET" action="{{ route('sales.deals.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama deal atau lead..."
                           class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 bg-white">
                        <option value="">Semua Status</option>
                        @foreach(config('beauty-crm.deal_statuses') as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Stage</label>
                    <select name="stage" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 bg-white">
                        <option value="">Semua Stage</option>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->id }}" {{ request('stage') == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari
                    </button>
                    <a href="{{ route('sales.deals.index') }}" class="px-4 py-2 text-charcoal-600 text-sm font-medium hover:text-charcoal-800 transition-colors">Reset</a>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Deals Table --}}
    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="deals-table">
                <thead>
                    <tr class="border-b border-charcoal-200 bg-charcoal-50/50">
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider">Deal</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider hidden sm:table-cell">Lead</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider">Value</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider hidden md:table-cell">Stage</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider hidden lg:table-cell">Expected Close</th>
                        <th class="text-right px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal-100">
                    @forelse($deals as $deal)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('sales.deals.show', $deal) }}" class="text-charcoal-800 font-medium hover:text-blue-600 transition-colors">
                                    {{ $deal->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                @if($deal->lead)
                                    <a href="{{ route('sales.leads.show', $deal->lead) }}" class="text-xs text-blue-600 hover:text-blue-700">{{ $deal->lead->name }}</a>
                                @else
                                    <span class="text-xs text-charcoal-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono font-semibold text-emerald-600">{{ $deal->formatted_value }}</span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                @if($deal->pipelineStage)
                                    <span class="inline-flex items-center gap-1.5 text-xs">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $deal->pipelineStage->color }}"></span>
                                        {{ $deal->pipelineStage->name }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :color="$deal->status_color" size="xs">{{ ucfirst($deal->status) }}</x-badge>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <span class="text-xs font-mono text-charcoal-500">
                                    {{ $deal->expected_close_date ? $deal->expected_close_date->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('sales.deals.show', $deal) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-charcoal-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                                </svg>
                                <p class="text-charcoal-500 text-sm">Belum ada deal.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($deals->hasPages())
            <div class="px-6 py-4 border-t border-charcoal-100">
                {{ $deals->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
