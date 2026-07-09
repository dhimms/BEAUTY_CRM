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
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Module</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">ID</th>
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
                            <div x-show="showDetail" x-cloak class="mt-2 p-3 bg-charcoal-50 rounded-lg text-xs">
                                @if($log->old_values)
                                    <p class="font-semibold text-charcoal-600 mb-1">Old:</p>
                                    <pre class="text-charcoal-500 whitespace-pre-wrap break-all">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                @endif
                                @if($log->new_values)
                                    <p class="font-semibold text-charcoal-600 mb-1 mt-2">New:</p>
                                    <pre class="text-charcoal-500 whitespace-pre-wrap break-all">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                @endif
                                @if($log->ip_address)
                                    <p class="text-charcoal-400 mt-2">IP: {{ $log->ip_address }}</p>
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
