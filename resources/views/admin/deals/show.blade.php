@extends('layouts.partials.app')
@section('title', 'Deal: ' . $deal->name)
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-charcoal-900 transition-colors">Dashboard</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.deals.index') }}" class="hover:text-charcoal-900 transition-colors">Deals</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li class="text-charcoal-900 font-medium">Deal Details</li>
@endsection
@section('page-actions')
    <a href="{{ route('admin.deals.edit', $deal) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Deal
    </a>
    <form action="{{ route('admin.deals.destroy', $deal) }}" method="POST" class="inline" onsubmit="return confirm('Delete this deal?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-rose-600 border border-transparent rounded-xl hover:bg-rose-700 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete
        </button>
    </form>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 space-y-6">
        {{-- Main Info --}}
        <x-card>
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-serif font-bold text-charcoal-900">{{ $deal->name }}</h2>
                    <p class="text-2xl font-semibold text-emerald-600 mt-2">Rp {{ number_format($deal->value, 0, ',', '.') }}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <x-badge :color="$deal->status_color" size="lg">{{ ucfirst($deal->status) }}</x-badge>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-charcoal-50 text-charcoal-800 border border-charcoal-200">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $deal->pipelineStage->color ?? '#F43F5E' }}"></span>
                        {{ $deal->pipelineStage->name }}
                    </span>
                </div>
            </div>

            @if($deal->status === 'lost')
                <div class="mt-4 p-4 rounded-xl bg-rose-50 border border-rose-100 flex items-start gap-3 text-rose-800">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <h4 class="font-bold text-sm">Lost Reason: {{ $deal->lostReason?->name ?? 'Not specified' }}</h4>
                        @if($deal->lost_notes)
                            <p class="mt-1 text-sm">{{ $deal->lost_notes }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-charcoal-100">
                <div>
                    <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold mb-1">Expected Close</p>
                    <p class="text-sm font-medium text-charcoal-900">{{ $deal->expected_close_date ? \Carbon\Carbon::parse($deal->expected_close_date)->format('M d, Y') : 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold mb-1">Actual Close</p>
                    <p class="text-sm font-medium text-charcoal-900">{{ $deal->closed_at ? $deal->closed_at->format('M d, Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-charcoal-400 uppercase tracking-wider font-semibold mb-1">Created At</p>
                    <p class="text-sm font-medium text-charcoal-900">{{ $deal->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </x-card>

        {{-- Activity Feed --}}
        <x-card padding="false">
            <div class="px-6 py-4 border-b border-charcoal-100">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Deal Activity History</h3>
            </div>
            <div class="px-6 py-6">
                @if($deal->activities->count() > 0)
                    <div class="relative pl-4 space-y-6 before:absolute before:inset-y-0 before:left-5 before:w-0.5 before:bg-charcoal-200">
                        @foreach($deal->activities as $activity)
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
                    <p class="text-center text-charcoal-500 py-4">No activities logged for this deal.</p>
                @endif
            </div>
        </x-card>
    </div>

    <div class="lg:col-span-1 space-y-6">
        {{-- Related Lead --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4 flex items-center justify-between">
                Associated Lead
                <a href="{{ route('admin.leads.show', $deal->lead) }}" class="text-sm font-sans font-medium text-blue-600 hover:underline">View Lead &rarr;</a>
            </h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-bold text-charcoal-900">{{ $deal->lead->name }}</p>
                    <p class="text-sm text-charcoal-600 mt-1">{{ $deal->lead->phone }}</p>
                    @if($deal->lead->email)
                        <p class="text-sm text-charcoal-600">{{ $deal->lead->email }}</p>
                    @endif
                </div>
                
                <div class="pt-4 border-t border-charcoal-100 flex justify-between items-center text-sm">
                    <span class="text-charcoal-500">Lead Status</span>
                    <x-badge :color="$deal->lead->status_color">{{ ucfirst($deal->lead->status) }}</x-badge>
                </div>
            </div>
        </x-card>

        {{-- Assigned Rep --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Assigned Sales Rep</h3>
            @if($deal->assignedUser)
                <div class="flex items-center gap-3">
                    <img src="{{ $deal->assignedUser->avatar_url }}" alt="" class="w-12 h-12 rounded-full border border-charcoal-200">
                    <div>
                        <p class="font-medium text-charcoal-900">{{ $deal->assignedUser->name }}</p>
                        <p class="text-sm text-charcoal-500">{{ $deal->assignedUser->email }}</p>
                    </div>
                </div>
            @else
                <div class="text-center py-4 bg-charcoal-50 rounded-xl border border-dashed border-charcoal-200">
                    <p class="text-sm text-charcoal-500">This deal is currently unassigned.</p>
                </div>
            @endif
        </x-card>
    </div>

</div>
@endsection
