@extends('layouts.partials.app')
@section('title', 'Lead Details: ' . $lead->name)
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-charcoal-900">Dashboard</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.leads.index') }}" class="hover:text-charcoal-900">Leads</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li class="text-charcoal-900 font-medium">Lead Details</li>
@endsection
@section('page-actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.leads.edit', $lead) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Left Column: Lead Info --}}
    <div class="lg:col-span-1 space-y-6">
        <x-card>
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-serif font-bold text-charcoal-900">{{ $lead->name }}</h2>
                    <p class="text-charcoal-500 mt-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $lead->phone }}
                    </p>
                </div>
                <x-badge :color="$lead->status_color" size="lg">{{ ucfirst($lead->status) }}</x-badge>
            </div>

            <div class="space-y-4 border-t border-charcoal-100 pt-4">
                @if($lead->email)
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-charcoal-50 rounded-lg text-charcoal-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                    <div>
                        <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold">Email</p>
                        <p class="text-sm text-charcoal-900">{{ $lead->email }}</p>
                    </div>
                </div>
                @endif
                
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-charcoal-50 rounded-lg text-charcoal-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    <div>
                        <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold">Address</p>
                        <p class="text-sm text-charcoal-900">{{ $lead->address ?? 'Not provided' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="p-2 bg-charcoal-50 rounded-lg text-charcoal-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                    <div>
                        <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold">Source</p>
                        <p class="text-sm text-charcoal-900 flex items-center gap-1">
                            {{ $lead->source?->icon }} {{ $lead->source?->name ?? 'Unknown' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="p-2 bg-charcoal-50 rounded-lg text-charcoal-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                    <div>
                        <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold">Assigned To</p>
                        @if($lead->assignedUser)
                            <div class="flex items-center gap-2 mt-1">
                                <img src="{{ $lead->assignedUser->avatar_url }}" alt="" class="w-5 h-5 rounded-full">
                                <p class="text-sm text-charcoal-900">{{ $lead->assignedUser->name }}</p>
                            </div>
                        @else
                            <p class="text-sm text-charcoal-500 italic mt-1">Unassigned</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($lead->notes)
            <div class="mt-6 pt-4 border-t border-charcoal-100">
                <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold mb-2">Notes</p>
                <div class="text-sm text-charcoal-700 bg-amber-50 p-3 rounded-lg border border-amber-100 whitespace-pre-line">{{ $lead->notes }}</div>
            </div>
            @endif
        </x-card>
    </div>

    {{-- Right Column: Deals & Activities --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Deals Section --}}
        <x-card padding="false">
            <div class="px-6 py-4 border-b border-charcoal-100 flex justify-between items-center">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Deals</h3>
            </div>
            <div class="p-0">
                @if($lead->deals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-charcoal-500 uppercase bg-charcoal-50 border-b border-charcoal-100">
                                <tr>
                                    <th class="px-6 py-3 font-medium">Deal Name</th>
                                    <th class="px-6 py-3 font-medium">Value</th>
                                    <th class="px-6 py-3 font-medium">Stage</th>
                                    <th class="px-6 py-3 font-medium text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-charcoal-100">
                                @foreach($lead->deals as $deal)
                                <tr class="hover:bg-charcoal-50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-charcoal-900">
                                        <a href="{{ route('admin.deals.show', $deal) }}" class="hover:text-rose-600">{{ $deal->name }}</a>
                                    </td>
                                    <td class="px-6 py-3 text-charcoal-700">Rp {{ number_format($deal->value, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium bg-charcoal-100 text-charcoal-800 border border-charcoal-200">
                                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $deal->pipelineStage->color ?? '#F43F5E' }}"></span>
                                            {{ $deal->pipelineStage->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <x-badge :color="$deal->status_color">{{ ucfirst($deal->status) }}</x-badge>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-charcoal-500">
                        <svg class="w-12 h-12 mx-auto text-charcoal-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p>No deals attached to this lead yet.</p>
                    </div>
                @endif
            </div>
        </x-card>

        {{-- Activity Feed --}}
        <x-card padding="false">
            <div class="px-6 py-4 border-b border-charcoal-100">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Activity History</h3>
            </div>
            <div class="px-6 py-6">
                @if($lead->activities->count() > 0)
                    <div class="relative pl-4 space-y-6 before:absolute before:inset-y-0 before:left-5 before:w-0.5 before:bg-charcoal-200">
                        @foreach($lead->activities as $activity)
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
