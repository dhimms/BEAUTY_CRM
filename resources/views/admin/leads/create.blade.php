@extends('layouts.partials.app')
@section('title', 'Add Lead')
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-charcoal-900 transition-colors">Dashboard</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.leads.index') }}" class="hover:text-charcoal-900 transition-colors">Leads</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li class="text-charcoal-900 font-medium">Add New</li>
@endsection
@section('page-header', 'Add New Lead')

@section('content')
<x-card class="max-w-4xl">
    <form action="{{ route('admin.leads.store') }}" method="POST" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            {{-- Column 1: Contact Info --}}
            <div class="space-y-6">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900 border-b border-charcoal-100 pb-2">Contact Information</h3>
                
                <div>
                    <label for="name" class="block text-sm font-medium text-charcoal-900 mb-1">Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                    @error('name') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-charcoal-900 mb-1">Phone Number <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                    @error('phone') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-charcoal-900 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                    @error('email') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-charcoal-900 mb-1">Address</label>
                    <textarea name="address" id="address" rows="3" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">{{ old('address') }}</textarea>
                    @error('address') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Column 2: Status & Assignment --}}
            <div class="space-y-6">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900 border-b border-charcoal-100 pb-2">Status & Assignment</h3>
                
                <div>
                    <label for="lead_source_id" class="block text-sm font-medium text-charcoal-900 mb-1">Lead Source <span class="text-rose-500">*</span></label>
                    <select name="lead_source_id" id="lead_source_id" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                        <option value="">Select source...</option>
                        @foreach($sources as $source)
                            <option value="{{ $source->id }}" {{ old('lead_source_id') == $source->id ? 'selected' : '' }}>{{ $source->icon }} {{ $source->name }}</option>
                        @endforeach
                    </select>
                    @error('lead_source_id') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-charcoal-900 mb-1">Assign To</label>
                    <select name="assigned_to" id="assigned_to" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                        <option value="">Unassigned</option>
                        @foreach($salesList as $sales)
                            <option value="{{ $sales->id }}" {{ old('assigned_to') == $sales->id ? 'selected' : '' }}>{{ $sales->name }}</option>
                        @endforeach
                    </select>
                    @error('assigned_to') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-charcoal-900 mb-1">Status <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                            <option value="new" {{ old('status', 'new') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="contacted" {{ old('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="qualified" {{ old('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                        </select>
                        @error('status') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="qualification" class="block text-sm font-medium text-charcoal-900 mb-1">Qualification</label>
                        <select name="qualification" id="qualification" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                            <option value="">Pending Evaluation</option>
                            <option value="qualified" {{ old('qualification') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                            <option value="unqualified" {{ old('qualification') == 'unqualified' ? 'selected' : '' }}>Unqualified</option>
                            <option value="not_fit" {{ old('qualification') == 'not_fit' ? 'selected' : '' }}>Not Fit</option>
                        </select>
                        @error('qualification') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-charcoal-900 mb-1">Internal Notes</label>
                    <textarea name="notes" id="notes" rows="4" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-charcoal-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.leads.index') }}" class="px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50 transition-colors">Cancel</a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors">Create Lead</button>
        </div>
    </form>
</x-card>
@endsection
