@extends('layouts.partials.app')
@section('title', 'User Profile: ' . $user->name)
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-charcoal-900 transition-colors">Dashboard</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.users.index') }}" class="hover:text-charcoal-900 transition-colors">Users</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li class="text-charcoal-900 font-medium">{{ $user->name }}</li>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    {{-- Profile Card --}}
    <div class="md:col-span-1 space-y-6">
        <x-card class="text-center">
            <div class="relative inline-block mb-4">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-sm mx-auto">
                <div class="absolute bottom-1 right-1 w-4 h-4 rounded-full border-2 border-white {{ $user->is_active ? 'bg-emerald-500' : 'bg-charcoal-300' }}" title="{{ $user->is_active ? 'Active' : 'Inactive' }}"></div>
            </div>
            <h2 class="text-xl font-serif font-bold text-charcoal-900">{{ $user->name }}</h2>
            <p class="text-charcoal-500 text-sm mb-4">{{ $user->email }}</p>
            
            <x-badge :color="$user->role_badge_color" size="md">{{ $user->getRoleNames()->first() ?? 'No Role' }}</x-badge>

            <div class="mt-6 pt-6 border-t border-charcoal-100 grid grid-cols-2 gap-4 text-left">
                <div>
                    <p class="text-xs text-charcoal-400 uppercase font-mono tracking-wider mb-1">Phone</p>
                    <p class="text-sm text-charcoal-800 font-medium">{{ $user->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-charcoal-400 uppercase font-mono tracking-wider mb-1">Joined</p>
                    <p class="text-sm text-charcoal-800 font-medium">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-2">
                @if(!$user->hasRole('Manager'))
                <a href="{{ route('admin.users.edit', $user) }}" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Profile
                </a>
                @else
                <p class="text-xs text-center text-charcoal-400 italic">User dengan role Manager hanya bisa diaktifkan atau dinonaktifkan.</p>
                @endif
            </div>
        </x-card>
    </div>

    {{-- Stats & Activities (Placeholder for future) --}}
    <div class="md:col-span-2 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-kpi-card label="Assigned Leads" value="{{ $leadCount }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' />" color="blue" :trendUp="true" />
            <x-kpi-card label="Assigned Deals" value="{{ $dealCount }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' />" color="amber" :trendUp="true" />
            <x-kpi-card label="Won Deals" value="{{ $wonDeals }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7' />" color="emerald" :trendUp="true" />
        </div>

        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Recent Audit Logs</h3>
            <div class="text-center py-8 text-charcoal-500">
                <svg class="w-12 h-12 mx-auto text-charcoal-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p>To view detailed logs, go to the <a href="{{ route('admin.audit-logs.index', ['user_id' => $user->id]) }}" class="text-rose-600 hover:underline">Audit Logs</a> module.</p>
            </div>
        </x-card>
    </div>
</div>
@endsection
