@extends('layouts.partials.app')
@section('title', 'Edit Deal: ' . $deal->name)
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-charcoal-900 transition-colors">Dashboard</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.deals.index') }}" class="hover:text-charcoal-900 transition-colors">Deals</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.deals.show', $deal) }}" class="hover:text-charcoal-900 transition-colors">{{ Str::limit($deal->name, 20) }}</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li class="text-charcoal-900 font-medium">Edit</li>
@endsection
@section('page-header', 'Edit Deal')

@section('content')
<x-card class="max-w-3xl" x-data="{ status: '{{ old('status', $deal->status) }}' }">
    <form action="{{ route('admin.deals.update', $deal) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-4 bg-charcoal-50 rounded-xl border border-charcoal-100 flex items-center justify-between mb-8">
            <div>
                <p class="text-xs text-charcoal-500 uppercase tracking-wider font-semibold mb-1">Associated Lead</p>
                <p class="font-medium text-charcoal-900">{{ $deal->lead->name }} ({{ $deal->lead->phone }})</p>
            </div>
            <a href="{{ route('admin.leads.show', $deal->lead) }}" class="text-sm text-blue-600 hover:underline" target="_blank">View Lead</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-charcoal-900 mb-1">Deal Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $deal->name) }}" required class="w-full px-4 py-2 bg-white border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                @error('name') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="value" class="block text-sm font-medium text-charcoal-900 mb-1">Deal Value (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" name="value" id="value" value="{{ old('value', $deal->value) }}" required min="0" class="w-full px-4 py-2 bg-white border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                @error('value') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="expected_close_date" class="block text-sm font-medium text-charcoal-900 mb-1">Expected Close Date</label>
                <input type="date" name="expected_close_date" id="expected_close_date" value="{{ old('expected_close_date', $deal->expected_close_date ? \Carbon\Carbon::parse($deal->expected_close_date)->format('Y-m-d') : '') }}" class="w-full px-4 py-2 bg-white border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                @error('expected_close_date') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="pipeline_stage_id" class="block text-sm font-medium text-charcoal-900 mb-1">Pipeline Stage <span class="text-rose-500">*</span></label>
                <select name="pipeline_stage_id" id="pipeline_stage_id" required class="w-full px-4 py-2 bg-white border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                    @foreach($stages as $stage)
                        <option value="{{ $stage->id }}" {{ old('pipeline_stage_id', $deal->pipeline_stage_id) == $stage->id ? 'selected' : '' }}>{{ $stage->name }} ({{ $stage->probability }}%)</option>
                    @endforeach
                </select>
                @error('pipeline_stage_id') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="assigned_to" class="block text-sm font-medium text-charcoal-900 mb-1">Assigned Sales Rep</label>
                <select name="assigned_to" id="assigned_to" class="w-full px-4 py-2 bg-white border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                    <option value="">Unassigned</option>
                    @foreach($salesList as $sales)
                        <option value="{{ $sales->id }}" {{ old('assigned_to', $deal->assigned_to) == $sales->id ? 'selected' : '' }}>{{ $sales->name }}</option>
                    @endforeach
                </select>
                @error('assigned_to') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 pt-4 border-t border-charcoal-100">
                <label for="status" class="block text-sm font-medium text-charcoal-900 mb-3">Deal Status <span class="text-rose-500">*</span></label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" x-model="status" name="status" value="open" class="text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-charcoal-800">Open</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" x-model="status" name="status" value="won" class="text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm font-medium text-charcoal-800">Won</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" x-model="status" name="status" value="lost" class="text-rose-600 focus:ring-rose-500">
                        <span class="text-sm font-medium text-charcoal-800">Lost</span>
                    </label>
                </div>
                @error('status') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 space-y-4" x-show="status === 'lost'" x-transition style="display: none;">
                <div class="p-4 bg-rose-50 rounded-xl border border-rose-100">
                    <label for="lost_reason_id" class="block text-sm font-medium text-rose-900 mb-1">Reason for Loss <span class="text-rose-600">*</span></label>
                    <select name="lost_reason_id" id="lost_reason_id" :required="status === 'lost'" class="w-full px-4 py-2 bg-white border border-rose-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                        <option value="">Select a reason...</option>
                        @foreach($lostReasons as $reason)
                            <option value="{{ $reason->id }}" {{ old('lost_reason_id', $deal->lost_reason_id) == $reason->id ? 'selected' : '' }}>{{ $reason->name }}</option>
                        @endforeach
                    </select>
                    @error('lost_reason_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror

                    <label for="lost_notes" class="block text-sm font-medium text-rose-900 mb-1 mt-4">Additional Notes</label>
                    <textarea name="lost_notes" id="lost_notes" rows="2" class="w-full px-4 py-2 bg-white border border-rose-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">{{ old('lost_notes', $deal->lost_notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-charcoal-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.deals.show', $deal) }}" class="px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50 transition-colors">Cancel</a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors">Save Changes</button>
        </div>
    </form>
</x-card>
@endsection
