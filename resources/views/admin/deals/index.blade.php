@extends('layouts.partials.app')
@section('title', 'Deals Management')
@section('page-header', 'Deals Management')

@section('content')
<x-card class="mb-6">
    <form action="{{ route('admin.deals.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search deal name or lead name..." class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
        </div>
        
        <div class="w-full md:w-48">
            <select name="status" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="won" {{ request('status') === 'won' ? 'selected' : '' }}>Won</option>
                <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
            </select>
        </div>

        <div class="w-full md:w-56">
            <select name="stage" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm" onchange="this.form.submit()">
                <option value="">All Pipeline Stages</option>
                @foreach($stages as $stage)
                    <option value="{{ $stage->id }}" {{ request('stage') == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 bg-charcoal-100 text-charcoal-700 hover:bg-charcoal-200 rounded-xl text-sm font-medium transition-colors">Filter</button>
            @if(request()->hasAny(['search', 'status', 'stage']))
                <a href="{{ route('admin.deals.index') }}" class="ml-2 px-4 py-2 text-rose-600 hover:text-rose-700 text-sm font-medium">Reset</a>
            @endif
        </div>
    </form>
</x-card>

<x-card padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-charcoal-500 uppercase bg-charcoal-50 border-b border-charcoal-200">
                <tr>
                    <th class="px-6 py-4 font-medium">Deal</th>
                    <th class="px-6 py-4 font-medium">Lead Contact</th>
                    <th class="px-6 py-4 font-medium">Value</th>
                    <th class="px-6 py-4 font-medium">Stage</th>
                    <th class="px-6 py-4 font-medium text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($deals as $deal)
                    <tr class="hover:bg-charcoal-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.deals.show', $deal) }}" class="font-medium text-charcoal-900 hover:text-rose-600 mb-1 block">{{ $deal->name }}</a>
                            <div class="flex items-center gap-2 text-xs text-charcoal-500">
                                @if($deal->assignedUser)
                                    <img src="{{ $deal->assignedUser->avatar_url }}" alt="" class="w-4 h-4 rounded-full">
                                    {{ $deal->assignedUser->name }}
                                @else
                                    <span class="italic">Unassigned</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.leads.show', $deal->lead) }}" class="text-blue-600 hover:underline font-medium">{{ $deal->lead->name }}</a>
                            <div class="text-xs text-charcoal-500 mt-1">{{ $deal->lead->phone }}</div>
                        </td>
                        <td class="px-6 py-4 font-medium text-emerald-700">
                            Rp {{ number_format($deal->value, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium bg-charcoal-100 text-charcoal-800 border border-charcoal-200 whitespace-nowrap">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $deal->pipelineStage->color ?? '#F43F5E' }}"></span>
                                {{ $deal->pipelineStage->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <x-badge :color="$deal->status_color">{{ ucfirst($deal->status) }}</x-badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-charcoal-500">
                            No deals found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deals->hasPages())
        <div class="px-6 py-4 border-t border-charcoal-200">
            {{ $deals->links() }}
        </div>
    @endif
</x-card>
@endsection
