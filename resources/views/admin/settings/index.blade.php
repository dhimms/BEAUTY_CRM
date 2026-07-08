@extends('layouts.partials.app')
@section('title', 'System Settings')
@section('page-header', 'System Settings')
@section('page-subtitle', 'Configure system variables and preferences')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Company Profile --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4 border-b border-charcoal-100 pb-2">Company Profile</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-charcoal-900 mb-1">Company Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $settings['company_name']) }}" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                    @error('company_name') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="company_email" class="block text-sm font-medium text-charcoal-900 mb-1">Company Email</label>
                    <input type="email" name="company_email" id="company_email" value="{{ old('company_email', $settings['company_email']) }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                    @error('company_email') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="company_phone" class="block text-sm font-medium text-charcoal-900 mb-1">Company Phone</label>
                    <input type="text" name="company_phone" id="company_phone" value="{{ old('company_phone', $settings['company_phone']) }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                    @error('company_phone') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="company_address" class="block text-sm font-medium text-charcoal-900 mb-1">Company Address</label>
                    <textarea name="company_address" id="company_address" rows="3" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">{{ old('company_address', $settings['company_address']) }}</textarea>
                    @error('company_address') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </x-card>

        {{-- Notifications --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4 border-b border-charcoal-100 pb-2">Notification Preferences</h3>
            
            <div class="space-y-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="notify_new_lead" value="1" class="mt-1 w-5 h-5 text-rose-500 border-charcoal-300 rounded focus:ring-rose-500" {{ old('notify_new_lead', $settings['notify_new_lead']) ? 'checked' : '' }}>
                    <div>
                        <span class="text-sm font-medium text-charcoal-900">Notify assigned rep on new lead assignment</span>
                        <p class="text-xs text-charcoal-500">Sends internal system alert when a new lead is assigned to a sales agent.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="notify_won_deal" value="1" class="mt-1 w-5 h-5 text-rose-500 border-charcoal-300 rounded focus:ring-rose-500" {{ old('notify_won_deal', $settings['notify_won_deal']) ? 'checked' : '' }}>
                    <div>
                        <span class="text-sm font-medium text-charcoal-900">Notify manager on won deals</span>
                        <p class="text-xs text-charcoal-500">Sends internal system alert to managers when a deal is closed won.</p>
                    </div>
                </label>
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700 focus:ring-4 focus:ring-rose-200 transition-colors">Save Settings</button>
        </div>
    </form>
</div>
@endsection
