@extends('layouts.partials.app')
@section('title', 'Customer Profile: ' . $customer->name)
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-charcoal-900 transition-colors">Dashboard</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.customers.index') }}" class="hover:text-charcoal-900 transition-colors">Customers</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li class="text-charcoal-900 font-medium">Customer Profile</li>
@endsection
@section('page-actions')
    <a href="{{ route('admin.customers.edit', $customer) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Profile
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Left Column: Profile Info --}}
    <div class="lg:col-span-1 space-y-6">
        <x-card>
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-3xl font-serif font-bold mx-auto mb-4">
                    {{ substr($customer->name, 0, 1) }}
                </div>
                <h2 class="text-xl font-serif font-bold text-charcoal-900">{{ $customer->name }}</h2>
                <div class="mt-2">
                    <x-badge :color="$customer->status_color">{{ ucfirst($customer->status) }}</x-badge>
                </div>
            </div>

            <div class="space-y-4 border-t border-charcoal-100 pt-4">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-charcoal-50 rounded-lg text-charcoal-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></div>
                    <div>
                        <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold">Phone</p>
                        <p class="text-sm text-charcoal-900 font-medium">{{ $customer->phone }}</p>
                    </div>
                </div>

                @if($customer->email)
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-charcoal-50 rounded-lg text-charcoal-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                    <div>
                        <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold">Email</p>
                        <p class="text-sm text-charcoal-900 font-medium">{{ $customer->email }}</p>
                    </div>
                </div>
                @endif
                
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-charcoal-50 rounded-lg text-charcoal-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    <div>
                        <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold">Address</p>
                        <p class="text-sm text-charcoal-900">{{ $customer->address ?? 'Not provided' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="p-2 bg-charcoal-50 rounded-lg text-charcoal-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                    <div>
                        <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold">Customer Since</p>
                        <p class="text-sm text-charcoal-900">{{ $customer->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            @if(is_array($customer->tags) && count($customer->tags) > 0)
            <div class="mt-6 pt-4 border-t border-charcoal-100">
                <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold mb-2">Tags</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($customer->tags as $tag)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-charcoal-100 text-charcoal-700">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($customer->notes)
            <div class="mt-6 pt-4 border-t border-charcoal-100">
                <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold mb-2">Internal Notes</p>
                <div class="text-sm text-charcoal-700 bg-amber-50 p-3 rounded-lg border border-amber-100 whitespace-pre-line">{{ $customer->notes }}</div>
            </div>
            @endif
        </x-card>
    </div>

    {{-- Right Column: Stats, Tickets, Activities --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Lifetime Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-kpi-card label="Total Spent" value="Rp {{ number_format($customer->total_spent, 0, ',', '.') }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' />" color="emerald" />
            <x-kpi-card label="Last Visit" value="{{ $customer->last_visit_date ? \Carbon\Carbon::parse($customer->last_visit_date)->format('d M Y') : 'Never' }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' />" color="blue" />
            <x-kpi-card label="Service Tickets" value="{{ $customer->serviceTickets->count() }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' />" color="amber" />
        </div>

        {{-- Activity Feed --}}
        <x-card padding="false">
            <div class="px-6 py-4 border-b border-charcoal-100 flex justify-between items-center">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Customer Activity Feed</h3>
            </div>
            <div class="px-6 py-6">
                @if($customer->activities->count() > 0)
                    <div class="relative pl-4 space-y-6 before:absolute before:inset-y-0 before:left-5 before:w-0.5 before:bg-charcoal-200">
                        @foreach($customer->activities as $activity)
                            <div class="relative flex gap-4">
                                <div class="absolute -left-6 bg-white p-1 rounded-full border-2 border-charcoal-200 z-10" title="{{ $activity->user->name }}">
                                    <img src="{{ $activity->user->avatar_url }}" alt="" class="w-6 h-6 rounded-full">
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-charcoal-900">{{ $activity->user->name }}</span>
                                        <x-badge :color="$activity->type_color" size="sm">{{ $activity->type }}</x-badge>
                                        <span class="text-xs text-charcoal-400">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-charcoal-700 mt-1">{{ $activity->subject }}</p>
                                    @if($activity->description)
                                        <div class="mt-2 text-sm text-charcoal-600 bg-charcoal-50 p-3 rounded-lg border border-charcoal-100 whitespace-pre-line">{{ $activity->description }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-charcoal-500 py-4">No activities logged yet.</p>
                @endif
            </div>
        </x-card>

    </div>
</div>
@endsection
