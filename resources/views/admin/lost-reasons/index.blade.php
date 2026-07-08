@extends('layouts.partials.app')
@section('title', 'Lost Reasons')
@section('page-header', 'Lost Reasons')
@section('page-actions')
    <a href="{{ route('admin.lost-reasons.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700 focus:ring-4 focus:ring-rose-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Lost Reason
    </a>
@endsection

@section('content')
<x-card padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-charcoal-500 uppercase bg-charcoal-50 border-b border-charcoal-200">
                <tr>
                    <th class="px-6 py-4 font-medium">Reason</th>
                    <th class="px-6 py-4 font-medium">Description</th>
                    <th class="px-6 py-4 font-medium">Deals Lost</th>
                    <th class="px-6 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($reasons as $reason)
                    <tr class="hover:bg-charcoal-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-charcoal-900">{{ $reason->name }}</td>
                        <td class="px-6 py-4 text-charcoal-600">{{ $reason->description ?? '-' }}</td>
                        <td class="px-6 py-4 text-charcoal-600 font-medium">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-charcoal-100 text-charcoal-700 text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                                {{ $reason->deals_count }} Deals
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.lost-reasons.edit', $reason) }}" class="text-amber-600 hover:text-amber-800 text-sm font-medium">Edit</a>
                            <form action="{{ route('admin.lost-reasons.destroy', $reason) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this reason?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-sm font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-charcoal-500">
                            No lost reasons found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reasons->hasPages())
        <div class="px-6 py-4 border-t border-charcoal-200">
            {{ $reasons->links() }}
        </div>
    @endif
</x-card>
@endsection
