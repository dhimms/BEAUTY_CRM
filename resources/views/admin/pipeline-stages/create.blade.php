@extends('layouts.partials.app')
@section('title', 'Add Pipeline Stage')
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-charcoal-900">Dashboard</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.pipeline-stages.index') }}" class="hover:text-charcoal-900">Pipeline Stages</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li class="text-charcoal-900 font-medium">Add New</li>
@endsection
@section('page-header', 'Add Pipeline Stage')

@section('content')
<x-card class="max-w-2xl">
    <form action="{{ route('admin.pipeline-stages.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="order" value="{{ $maxOrder + 1 }}">

        <div>
            <label for="name" class="block text-sm font-medium text-charcoal-900 mb-1">Stage Name <span class="text-rose-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm" placeholder="e.g., New Lead, Qualified, Proposal">
            @error('name') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="probability" class="block text-sm font-medium text-charcoal-900 mb-1">Probability (%) <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="number" name="probability" id="probability" value="{{ old('probability', 0) }}" min="0" max="100" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm pr-8">
                    <span class="absolute right-3 top-2.5 text-charcoal-500">%</span>
                </div>
                <p class="mt-1 text-xs text-charcoal-500">Estimated chance of winning a deal at this stage.</p>
                @error('probability') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="color" class="block text-sm font-medium text-charcoal-900 mb-1">Color Marker</label>
                <div class="flex gap-2">
                    <input type="color" name="color" id="color" value="{{ old('color', '#3B82F6') }}" class="h-10 w-14 rounded-xl cursor-pointer bg-charcoal-50 border border-charcoal-200">
                    <input type="text" value="{{ old('color', '#3B82F6') }}" oninput="document.getElementById('color').value = this.value" class="flex-1 px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                </div>
                @error('color') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-charcoal-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.pipeline-stages.index') }}" class="px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50">Cancel</a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700">Save Stage</button>
        </div>
    </form>
</x-card>
@endsection
