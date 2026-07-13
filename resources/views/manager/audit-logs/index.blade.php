@extends('layouts.partials.app')
@section('title', 'Audit Logs')
@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Audit Logs</li>
@endsection
@section('page-header', 'Audit Log')
@section('page-subtitle', 'Catatan semua perubahan data dalam sistem (read-only)')

@section('content')
<x-card :padding="false">
    {{-- Filters --}}
    <div class="p-4 border-b border-charcoal-100">
        <form method="GET" action="{{ route('manager.audit-logs.index') }}" class="flex flex-wrap items-center gap-3">
            <select name="action" class="px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-amber-500">
                <option value="">Semua Action</option>
                <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
            </select>
            <select name="user_id" class="px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-amber-500">
                <option value="">Semua User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <input type="text" name="module" value="{{ request('module') }}" placeholder="Filter module..."
                class="px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
            <button type="submit" class="px-4 py-2.5 bg-charcoal-800 text-white rounded-xl text-sm font-medium hover:bg-charcoal-900 transition-colors">Filter</button>
            @if(request()->hasAny(['action', 'user_id', 'module']))
                <a href="{{ route('manager.audit-logs.index') }}" class="text-charcoal-500 hover:text-charcoal-700 text-sm">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Dilakukan Oleh</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Data yg Diubah</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">ID Data</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-charcoal-50/30 transition-colors" x-data="{ showDetail: false }">
                        <td class="px-6 py-3 text-charcoal-500 text-xs font-mono whitespace-nowrap">
                            {{ $log->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-3 text-charcoal-700">{{ $log->user?->name ?? '-' }}</td>
                        <td class="px-6 py-3">
                            <x-badge :color="$log->action_color" size="xs">{{ ucfirst($log->action) }}</x-badge>
                        </td>
                        <td class="px-6 py-3 text-charcoal-600 font-mono text-xs">{{ $log->module_name }}</td>
                        <td class="px-6 py-3 text-charcoal-400 font-mono text-xs">#{{ $log->auditable_id }}</td>
                        <td class="px-6 py-3">
                            <button @click="showDetail = !showDetail" class="text-amber-600 hover:text-amber-700 text-xs font-medium">
                                <span x-text="showDetail ? 'Tutup' : 'Lihat'"></span>
                            </button>
                            <div x-show="showDetail" x-cloak class="mt-3 p-4 bg-white border border-charcoal-200 rounded-xl shadow-sm text-xs relative">
                                <div class="grid grid-cols-1 gap-2 max-w-4xl">
                                    @php
                                        $changedKeys = [];
                                        if ($log->action === 'updated' && is_array($log->new_values)) {
                                            $changedKeys = array_keys($log->new_values);
                                        } elseif ($log->action === 'created' && is_array($log->new_values)) {
                                            $changedKeys = array_keys($log->new_values);
                                        } elseif ($log->action === 'deleted' && is_array($log->old_values)) {
                                            $changedKeys = array_keys($log->old_values);
                                        }
                                    @endphp

                                    @foreach($changedKeys as $key)
                                        @php
                                            $oldVal = $log->old_values[$key] ?? '-';
                                            $newVal = $log->new_values[$key] ?? '-';
                                            
                                            if (is_array($oldVal)) $oldVal = 'Array/Object';
                                            if (is_array($newVal)) $newVal = 'Array/Object';
                                        @endphp
                                        
                                        <div class="flex items-center gap-4 bg-white p-3 rounded-xl border border-charcoal-100 shadow-sm">
                                            <div class="w-1/4 shrink-0">
                                                <span class="text-xs font-mono text-charcoal-500 font-semibold uppercase tracking-wider">{{ str_replace('_', ' ', $key) }}</span>
                                            </div>
                                            
                                            <div class="flex-1 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 overflow-hidden">
                                                @if($log->action === 'updated')
                                                    <div class="flex-1 px-3 py-2 bg-rose-50 border border-rose-100 text-rose-600 rounded-lg text-sm line-through truncate" title="{{ $oldVal }}">
                                                        {{ $oldVal === '' || $oldVal === null ? '(Kosong)' : $oldVal }}
                                                    </div>
                                                    <div class="hidden sm:flex shrink-0 text-charcoal-300">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                    </div>
                                                    <div class="flex-1 px-3 py-2 bg-emerald-50 border border-emerald-100 text-emerald-700 font-medium rounded-lg text-sm truncate" title="{{ $newVal }}">
                                                        {{ $newVal === '' || $newVal === null ? '(Kosong)' : $newVal }}
                                                    </div>
                                                @elseif($log->action === 'created')
                                                    <div class="flex-1 px-3 py-2 bg-emerald-50 border border-emerald-100 text-emerald-700 font-medium rounded-lg text-sm truncate" title="{{ $newVal }}">
                                                        {{ $newVal === '' || $newVal === null ? '(Kosong)' : $newVal }}
                                                    </div>
                                                @elseif($log->action === 'deleted')
                                                    <div class="flex-1 px-3 py-2 bg-rose-50 border border-rose-100 text-rose-600 rounded-lg text-sm truncate" title="{{ $oldVal }}">
                                                        {{ $oldVal === '' || $oldVal === null ? '(Kosong)' : $oldVal }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    @if(empty($changedKeys))
                                        <div class="text-center py-4 text-charcoal-400 text-sm">Tidak ada detail perubahan (atau format data tidak didukung).</div>
                                    @endif
                                </div>
                                @if($log->ip_address)
                                    <div class="mt-4 pt-3 border-t border-charcoal-100 flex items-center gap-4 text-charcoal-400 font-mono text-[10px]">
                                        <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg> {{ $log->ip_address }}</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-charcoal-400">
                            Belum ada audit log.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="p-4 border-t border-charcoal-100">{{ $logs->links() }}</div>
    @endif
</x-card>
@endsection
