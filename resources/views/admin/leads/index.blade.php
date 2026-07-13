@extends('layouts.partials.app')
@section('title', 'Leads Management')
@section('page-header', 'Leads Management')
@section('page-actions')
<div class="flex gap-2">
    <button type="button" @click="$dispatch('open-import-modal')" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50 focus:ring-4 focus:ring-charcoal-100 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        Import
    </button>

    {{-- Export Dropdown dengan pilihan periode --}}
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50 focus:ring-4 focus:ring-charcoal-100 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-charcoal-200 z-50 overflow-hidden" x-cloak>
            <p class="px-4 py-2 text-[10px] uppercase font-mono tracking-wider text-charcoal-400">Pilih Periode</p>
            @php
                $baseQuery = request()->except('period');
            @endphp
            <a href="{{ route('admin.leads.export', $baseQuery) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-charcoal-700 hover:bg-charcoal-50 transition-colors">
                <svg class="w-4 h-4 text-charcoal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"/></svg>
                Semua Data
            </a>
            <a href="{{ route('admin.leads.export', array_merge($baseQuery, ['period' => 'today'])) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-charcoal-700 hover:bg-charcoal-50 transition-colors">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Hari Ini
            </a>
            <a href="{{ route('admin.leads.export', array_merge($baseQuery, ['period' => 'this_week'])) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-charcoal-700 hover:bg-charcoal-50 transition-colors">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Minggu Ini
            </a>
            <a href="{{ route('admin.leads.export', array_merge($baseQuery, ['period' => 'this_month'])) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-charcoal-700 hover:bg-charcoal-50 transition-colors">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Bulan Ini
            </a>
            <a href="{{ route('admin.leads.export', array_merge($baseQuery, ['period' => 'this_year'])) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-charcoal-700 hover:bg-charcoal-50 border-t border-charcoal-100 transition-colors">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Tahun Ini
            </a>
        </div>
    </div>

    <a href="{{ route('admin.leads.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700 focus:ring-4 focus:ring-rose-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Lead
    </a>
</div>
@endsection

@section('content')
{{-- Filter Section --}}
<x-card class="mb-6">
    <form action="{{ route('admin.leads.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="lg:col-span-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone, email..." class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
        </div>
        
        <div>
            <select name="status" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="" {{ request('status') === null ? 'selected' : '' }}>Active Leads (Exclude Converted)</option>
                @foreach(['new', 'contacted', 'qualified', 'converted', 'closed'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="source" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                <option value="">All Sources</option>
                @foreach($sources as $src)
                    <option value="{{ $src->id }}" {{ request('source') == $src->id ? 'selected' : '' }}>{{ $src->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="assigned_to" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                <option value="">All Assigned</option>
                <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                @foreach($salesList as $sales)
                    <option value="{{ $sales->id }}" {{ request('assigned_to') == $sales->id ? 'selected' : '' }}>{{ $sales->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-charcoal-800 text-white hover:bg-charcoal-900 rounded-xl text-sm font-medium transition-colors">Filter</button>
            @if(request()->anyFilled(['search', 'status', 'source', 'assigned_to', 'qualification']))
                <a href="{{ route('admin.leads.index') }}" class="p-2 text-rose-600 hover:text-rose-800" title="Reset Filters"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></a>
            @endif
        </div>

        {{-- Date Range Row --}}
        <div class="lg:col-span-2">
            <label class="block text-xs text-charcoal-500 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
        </div>
        <div class="lg:col-span-2">
            <label class="block text-xs text-charcoal-500 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
        </div>
    </form>
</x-card>

{{-- Data Table --}}
<x-card padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-charcoal-500 uppercase bg-charcoal-50 border-b border-charcoal-200">
                <tr>
                    <th class="px-6 py-4 font-medium">Lead Info</th>
                    <th class="px-6 py-4 font-medium">Contact</th>
                    <th class="px-6 py-4 font-medium">Source / Status</th>
                    <th class="px-6 py-4 font-medium">Assigned To</th>
                    <th class="px-6 py-4 font-medium">Date</th>
                    <th class="px-6 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($leads as $lead)
                    <tr class="hover:bg-charcoal-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.leads.show', $lead) }}" class="font-medium text-charcoal-900 hover:text-rose-600 transition-colors">{{ $lead->name }}</a>
                            @if($lead->qualification === 'qualified')
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-800">Qualified</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-charcoal-900 font-medium">{{ $lead->phone }}</div>
                            <div class="text-charcoal-500 text-xs">{{ $lead->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 space-y-1">
                            <div class="flex items-center gap-1.5 text-xs text-charcoal-600">
                                <span>{{ $lead->source?->icon ?? '🎯' }}</span>
                                {{ $lead->source?->name ?? 'Unknown' }}
                            </div>
                            <x-badge :color="$lead->status_color">{{ ucfirst($lead->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-4">
                            @if($lead->assignedUser)
                                <div class="flex items-center gap-2">
                                    <img src="{{ $lead->assignedUser->avatar_url }}" alt="" class="w-6 h-6 rounded-full">
                                    <span class="text-charcoal-700">{{ $lead->assignedUser->name }}</span>
                                </div>
                            @else
                                <span class="text-charcoal-400 italic">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-charcoal-600">
                            {{ $lead->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.leads.edit', $lead) }}" class="text-amber-600 hover:text-amber-800" title="Edit"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></a>
                            <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST" class="inline" onsubmit="return confirm('Delete this lead?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800" title="Delete"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-charcoal-500">
                            No leads found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($leads->hasPages())
        <div class="px-6 py-4 border-t border-charcoal-200">
            {{ $leads->links() }}
        </div>
    @endif
</x-card>

{{-- Import Modal --}}
<div x-data="{ open: false }" @open-import-modal.window="open = true" @keydown.escape.window="open = false" x-show="open" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
    
    {{-- Backdrop --}}
    <div x-show="open" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-charcoal-900 bg-opacity-75 transition-opacity"></div>

    {{-- Modal Panel --}}
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="open" 
                 @click.outside="open = false"
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <form action="{{ route('admin.leads.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-charcoal-900" id="modal-title">Import Leads</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-charcoal-500 mb-4">Upload an Excel or CSV file to import leads. Download the template below to ensure correct formatting.</p>
                                    
                                    <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="block w-full text-sm text-charcoal-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer">
                                    
                                    <div class="mt-4">
                                        <a href="{{ route('admin.leads.import.template') }}" class="text-sm text-blue-600 hover:underline">Download Template</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-charcoal-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-rose-600 text-base font-medium text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 sm:ml-3 sm:w-auto sm:text-sm">Import Now</button>
                        <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-charcoal-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-charcoal-700 hover:bg-charcoal-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-charcoal-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
