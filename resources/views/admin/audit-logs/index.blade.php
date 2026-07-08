@extends('layouts.partials.app')
@section('title', 'Audit Logs')
@section('page-header', 'Audit Logs')

@section('content')
<x-card class="mb-6">
    <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <select name="user_id" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm" onchange="this.form.submit()">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="action" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm" onchange="this.form.submit()">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <input type="text" name="module" value="{{ request('module') }}" placeholder="Module name (e.g. Lead, Deal)..." class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 bg-charcoal-100 text-charcoal-700 hover:bg-charcoal-200 rounded-xl text-sm font-medium transition-colors">Filter</button>
            @if(request()->hasAny(['user_id', 'action', 'module']))
                <a href="{{ route('admin.audit-logs.index') }}" class="ml-2 px-4 py-2 text-rose-600 hover:text-rose-700 text-sm font-medium">Reset</a>
            @endif
        </div>
    </form>
</x-card>

<x-card padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-charcoal-500 uppercase bg-charcoal-50 border-b border-charcoal-200">
                <tr>
                    <th class="px-6 py-4 font-medium">User</th>
                    <th class="px-6 py-4 font-medium">Action</th>
                    <th class="px-6 py-4 font-medium">Module / ID</th>
                    <th class="px-6 py-4 font-medium">Description</th>
                    <th class="px-6 py-4 font-medium">IP Address</th>
                    <th class="px-6 py-4 font-medium">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100" x-data="{ expanded: null }">
                @forelse($logs as $log)
                    <tr class="hover:bg-charcoal-50 cursor-pointer" @click="expanded = expanded === {{ $log->id }} ? null : {{ $log->id }}">
                        <td class="px-6 py-4">
                            @if($log->user)
                                <div class="flex items-center gap-2">
                                    <img src="{{ $log->user->avatar_url }}" alt="" class="w-6 h-6 rounded-full object-cover">
                                    <span class="font-medium text-charcoal-900">{{ $log->user->name }}</span>
                                </div>
                            @else
                                <span class="text-charcoal-400 italic">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeColor = match($log->action) {
                                    'created' => 'emerald',
                                    'updated' => 'blue',
                                    'deleted' => 'rose',
                                    default => 'charcoal'
                                };
                            @endphp
                            <x-badge :color="$badgeColor">{{ ucfirst($log->action) }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-charcoal-700 font-medium">
                            {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                        </td>
                        <td class="px-6 py-4 text-charcoal-600">
                            {{ $log->description }}
                            @if(!empty($log->old_values) || !empty($log->new_values))
                                <span class="text-xs text-rose-500 block hover:underline mt-1 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View changes
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-charcoal-500 font-mono text-xs">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-charcoal-500">
                            {{ $log->created_at->format('d M Y H:i:s') }}
                        </td>
                    </tr>
                    
                    {{-- Detail panel for diffs --}}
                    @if(!empty($log->old_values) || !empty($log->new_values))
                        <tr x-show="expanded === {{ $log->id }}" x-transition style="display: none;">
                            <td colspan="6" class="px-6 py-4 bg-charcoal-50 border-t border-b border-charcoal-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-xs uppercase font-mono font-bold tracking-wider text-charcoal-500 mb-2">Before Changes</h4>
                                        <pre class="bg-white border border-charcoal-200 p-4 rounded-xl text-xs text-charcoal-700 overflow-x-auto font-mono max-h-60">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                    <div>
                                        <h4 class="text-xs uppercase font-mono font-bold tracking-wider text-charcoal-500 mb-2">After Changes</h4>
                                        <pre class="bg-white border border-charcoal-200 p-4 rounded-xl text-xs text-charcoal-700 overflow-x-auto font-mono max-h-60">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-charcoal-500">
                            No logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-charcoal-200">
            {{ $logs->links() }}
        </div>
    @endif
</x-card>
@endsection
