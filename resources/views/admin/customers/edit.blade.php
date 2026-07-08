@extends('layouts.partials.app')
@section('title', 'Edit Customer: ' . $customer->name)
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-charcoal-900 transition-colors">Dashboard</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.customers.index') }}" class="hover:text-charcoal-900 transition-colors">Customers</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.customers.show', $customer) }}" class="hover:text-charcoal-900 transition-colors">{{ Str::limit($customer->name, 20) }}</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li class="text-charcoal-900 font-medium">Edit</li>
@endsection
@section('page-header', 'Edit Customer')

@section('content')
<x-card class="max-w-3xl">
    <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-charcoal-900 mb-1">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                @error('name') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-charcoal-900 mb-1">Phone Number <span class="text-rose-500">*</span></label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                @error('phone') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-charcoal-900 mb-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                @error('email') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-charcoal-900 mb-1">Address</label>
                <textarea name="address" id="address" rows="3" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">{{ old('address', $customer->address) }}</textarea>
                @error('address') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-charcoal-900 mb-1">Status <span class="text-rose-500">*</span></label>
                <select name="status" id="status" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                    <option value="active" {{ old('status', $customer->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $customer->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="churn" {{ old('status', $customer->status) === 'churn' ? 'selected' : '' }}>Churned</option>
                </select>
                @error('status') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="tags" class="block text-sm font-medium text-charcoal-900 mb-1">Tags (Comma separated)</label>
                <input type="text" name="tags" id="tags" value="{{ old('tags', is_array($customer->tags) ? implode(', ', $customer->tags) : '') }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm" placeholder="e.g., VIP, Acne Treatment, Regular">
                @error('tags') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-charcoal-900 mb-1">Internal Notes</label>
                <textarea name="notes" id="notes" rows="4" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">{{ old('notes', $customer->notes) }}</textarea>
                @error('notes') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-6 border-t border-charcoal-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.customers.show', $customer) }}" class="px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50 transition-colors">Cancel</a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors">Save Changes</button>
        </div>
    </form>
</x-card>
@endsection
