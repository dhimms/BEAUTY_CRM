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
    <div class="border-b border-charcoal-200">
        <nav class="flex -mb-px" aria-label="Tabs">
            <a href="{{ route('admin.audit-logs.index', ['tab' => 'system']) }}" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm {{ $tab === 'system' ? 'border-rose-500 text-rose-600' : 'border-transparent text-charcoal-500 hover:text-charcoal-700 hover:border-charcoal-300' }}">
                System CRUD Logs
            </a>
            <a href="{{ route('admin.audit-logs.index', ['tab' => 'activities']) }}" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm {{ $tab === 'activities' ? 'border-rose-500 text-rose-600' : 'border-transparent text-charcoal-500 hover:text-charcoal-700 hover:border-charcoal-300' }}">
                User Activities & Blast Logs
            </a>
        </nav>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-charcoal-500 uppercase bg-charcoal-50 border-b border-charcoal-200">
                <tr>
                    <th class="px-6 py-4 font-medium">User</th>
                    <th class="px-6 py-4 font-medium">{{ $tab === 'activities' ? 'Type' : 'Action' }}</th>
                    <th class="px-6 py-4 font-medium">{{ $tab === 'activities' ? 'Target Customer / Lead' : 'Module / ID' }}</th>
                    <th class="px-6 py-4 font-medium">Description</th>
                    @if($tab === 'system')
                        <th class="px-6 py-4 font-medium">IP Address</th>
                    @endif
                    <th class="px-6 py-4 font-medium">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100" x-data="{ expanded: null }">
                @forelse($logs as $log)
                    <tr class="hover:bg-charcoal-50 {{ $tab === 'system' ? 'cursor-pointer' : '' }}" @if($tab === 'system') @click="expanded = expanded === {{ $log->id }} ? null : {{ $log->id }}" @endif>
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
                            @if($tab === 'activities')
                                <x-badge :color="$log->type_color">{{ ucfirst($log->type) }}</x-badge>
                            @else
                                @php
                                    $badgeColor = match($log->action) {
                                        'created' => 'emerald',
                                        'updated' => 'blue',
                                        'deleted' => 'rose',
                                        default => 'charcoal'
                                    };
                                @endphp
                                <x-badge :color="$badgeColor">{{ ucfirst($log->action) }}</x-badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-charcoal-700 font-medium">
                            @if($tab === 'activities')
                                @if($log->activitable)
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4 text-charcoal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        {{ $log->activitable->name ?? 'Unknown' }}
                                    </span>
                                @else
                                    <span class="text-charcoal-400 italic">Deleted/Unknown</span>
                                @endif
                            @else
                                {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-charcoal-600">
                            {{ $tab === 'activities' ? ($log->description ?? $log->subject ?? '-') : $log->description }}
                            @if($tab === 'system' && (!empty($log->old_values) || !empty($log->new_values)))
                                <span class="text-xs text-rose-500 block hover:underline mt-1 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View changes
                                </span>
                            @endif
                        </td>
                        @if($tab === 'system')
                            <td class="px-6 py-4 text-charcoal-500 font-mono text-xs">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        @endif
                        <td class="px-6 py-4 text-charcoal-500">
                            {{ $tab === 'activities' ? $log->activity_date->format('d M Y H:i') : $log->created_at->format('d M Y H:i:s') }}
                        </td>
                    </tr>
                    
                    {{-- Detail panel for diffs (only for system tab) --}}
                    @if($tab === 'system' && (!empty($log->old_values) || !empty($log->new_values)))
                        <tr x-show="expanded === {{ $log->id }}" x-transition style="display: none;">
                            <td colspan="6" class="px-6 py-4 bg-charcoal-50 border-t border-b border-charcoal-200">
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

                                        // Menyaring kolom teknis agar tidak menuh-menuhin layar
                                        $ignoredKeys = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'password'];
                                        $changedKeys = array_filter($changedKeys, fn($key) => !in_array($key, $ignoredKeys));

                                        // Menyaring agar hanya menampilkan kolom yang benar-benar berubah nilainya
                                        if ($log->action === 'updated') {
                                            $changedKeys = array_filter($changedKeys, function($key) use ($log) {
                                                $old = $log->old_values[$key] ?? null;
                                                $new = $log->new_values[$key] ?? null;
                                                return $old !== $new;
                                            });
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
                                        <div class="text-center py-4 text-charcoal-400 text-sm">
                                            @if($log->action === 'created')
                                                Data baru berhasil ditambahkan ke sistem.
                                            @elseif($log->action === 'deleted')
                                                Data telah dihapus secara permanen dari sistem.
                                            @elseif($log->action === 'updated')
                                                Pembaruan disimpan (hanya sinkronisasi sistem/waktu, tidak ada data inti yang berubah).
                                            @else
                                                Aktivitas tercatat oleh sistem.
                                            @endif
                                        </div>
                                    @endif
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
