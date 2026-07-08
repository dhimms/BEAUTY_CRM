@extends('layouts.partials.app')
@section('title', 'User Management')
@section('page-header', 'User Management')
@section('page-actions')
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700 focus:ring-4 focus:ring-rose-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add User
    </a>
@endsection

@section('content')
<x-card class="mb-6">
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or phone..." class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
        </div>
        <div class="w-full md:w-48">
            <select name="role" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm" onchange="this.form.submit()">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-48">
            <select name="status" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex items-center">
            <button type="submit" class="px-4 py-2 bg-charcoal-100 text-charcoal-700 hover:bg-charcoal-200 rounded-xl text-sm font-medium transition-colors">Filter</button>
            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.users.index') }}" class="ml-2 px-4 py-2 text-rose-600 hover:text-rose-700 text-sm font-medium">Reset</a>
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
                    <th class="px-6 py-4 font-medium">Role</th>
                    <th class="px-6 py-4 font-medium">Phone</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100" x-data="userTable()">
                @forelse($users as $user)
                    <tr class="hover:bg-charcoal-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border border-charcoal-200">
                                <div>
                                    <div class="font-medium text-charcoal-900">{{ $user->name }}</div>
                                    <div class="text-xs text-charcoal-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :color="$user->role_badge_color">{{ $user->getRoleNames()->first() ?? 'No Role' }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-charcoal-600">{{ $user->phone ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <button @click="toggleStatus({{ $user->id }}, $event)" class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2" role="switch" :aria-checked="status[{{ $user->id }}] ? 'true' : 'false'">
                                <span aria-hidden="true" class="pointer-events-none absolute mx-auto h-4 w-8 rounded-full transition-colors duration-200 ease-in-out" :class="status[{{ $user->id }}] ? 'bg-emerald-500' : 'bg-charcoal-200'"></span>
                                <span aria-hidden="true" class="pointer-events-none absolute left-0 inline-block h-5 w-5 transform rounded-full border border-charcoal-200 bg-white shadow ring-0 transition-transform duration-200 ease-in-out" :class="status[{{ $user->id }}] ? 'translate-x-4' : 'translate-x-0'"></span>
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800" title="View"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg></a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-amber-600 hover:text-amber-800" title="Edit"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800" title="Delete"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-charcoal-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-charcoal-200">
            {{ $users->links() }}
        </div>
    @endif
</x-card>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('userTable', () => ({
            status: {
                @foreach($users as $user)
                {{ $user->id }}: {{ $user->is_active ? 'true' : 'false' }},
                @endforeach
            },
            toggleStatus(userId, event) {
                const btn = event.currentTarget;
                btn.disabled = true;
                
                fetch(`/admin/users/${userId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        this.status[userId] = data.is_active;
                    } else if(data.error) {
                        alert(data.error);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An error occurred.');
                })
                .finally(() => {
                    btn.disabled = false;
                });
            }
        }));
    });
</script>
@endpush
