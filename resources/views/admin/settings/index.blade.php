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

        {{-- API Configurations --}}
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4 border-b border-charcoal-100 pb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                API & Integrations
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <h4 class="text-sm font-bold text-charcoal-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp Blast API (Fonnte)
                    </h4>
                    <label for="fonnte_token" class="block text-sm font-medium text-charcoal-900 mb-1">Fonnte Token <span class="text-xs text-charcoal-400 font-normal">(Used for WhatsApp Blast)</span></label>
                    <input type="text" name="fonnte_token" id="fonnte_token" value="{{ old('fonnte_token', $settings['fonnte_token']) }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm font-mono" placeholder="e.g. Axbcd1234...">
                    <p class="text-xs text-charcoal-500 mt-1">Dapatkan token di <a href="https://fonnte.com" target="_blank" class="text-emerald-600 hover:underline">fonnte.com</a></p>
                </div>

                <div class="md:col-span-2 mt-4">
                    <h4 class="text-sm font-bold text-charcoal-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Email SMTP Configuration
                    </h4>
                </div>

                <div>
                    <label for="mail_host" class="block text-sm font-medium text-charcoal-900 mb-1">Mail Host</label>
                    <input type="text" name="mail_host" id="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="smtp.mailtrap.io">
                </div>

                <div>
                    <label for="mail_port" class="block text-sm font-medium text-charcoal-900 mb-1">Mail Port</label>
                    <input type="number" name="mail_port" id="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="587">
                </div>

                <div>
                    <label for="mail_username" class="block text-sm font-medium text-charcoal-900 mb-1">Mail Username</label>
                    <input type="text" name="mail_username" id="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                </div>

                <div>
                    <label for="mail_password" class="block text-sm font-medium text-charcoal-900 mb-1">Mail Password</label>
                    <input type="password" name="mail_password" id="mail_password" value="{{ old('mail_password', $settings['mail_password']) }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                </div>
                
                <div class="md:col-span-2">
                    <label for="mail_from_address" class="block text-sm font-medium text-charcoal-900 mb-1">Mail From Address <span class="text-xs text-charcoal-400 font-normal">(Sender Email)</span></label>
                    <input type="email" name="mail_from_address" id="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="hello@beautycrm.com">
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700 focus:ring-4 focus:ring-rose-200 transition-colors">Save Settings</button>
        </div>
    </form>
</div>
@endsection
