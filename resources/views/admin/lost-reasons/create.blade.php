@extends('layouts.partials.app')
@section('title', 'Add Lost Reason')
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-charcoal-900">Dashboard</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.lost-reasons.index') }}" class="hover:text-charcoal-900">Lost Reasons</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li class="text-charcoal-900 font-medium">Add New</li>
@endsection
@section('page-header', 'Add Lost Reason')

@section('content')
<x-card class="max-w-2xl">
    <form action="{{ route('admin.lost-reasons.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-charcoal-900 mb-1">Reason Name <span class="text-rose-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm" placeholder="e.g., Price Too High, Missing Features">
            @error('name') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-charcoal-900 mb-1">Description (Optional)</label>
            <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">{{ old('description') }}</textarea>
            @error('description') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-charcoal-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.lost-reasons.index') }}" class="px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50">Cancel</a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700">Save Reason</button>
        </div>
    </form>
</x-card>
@endsection
