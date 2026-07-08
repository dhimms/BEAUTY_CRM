@extends('layouts.partials.app')
@section('title', 'Add User')
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-charcoal-900 transition-colors">Dashboard</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="{{ route('admin.users.index') }}" class="hover:text-charcoal-900 transition-colors">Users</a></li>
    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li class="text-charcoal-900 font-medium">Add New User</li>
@endsection
@section('page-header', 'Add New User')

@section('content')
<x-card class="max-w-3xl">
    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Basic Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-charcoal-900 mb-1">Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                @error('name') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-charcoal-900 mb-1">Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                @error('email') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="phone" class="block text-sm font-medium text-charcoal-900 mb-1">Phone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                @error('phone') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-charcoal-900 mb-1">Role <span class="text-rose-500">*</span></label>
                <select name="role" id="role" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                    <option value="">Select a role...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Security --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="password" class="block text-sm font-medium text-charcoal-900 mb-1">Password <span class="text-rose-500">*</span></label>
                <input type="password" name="password" id="password" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
                @error('password') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-charcoal-900 mb-1">Confirm Password <span class="text-rose-500">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
            </div>
        </div>

        {{-- Avatar & Status --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <div>
                <label for="avatar" class="block text-sm font-medium text-charcoal-900 mb-1">Avatar Image (Optional)</label>
                <input type="file" name="avatar" id="avatar" accept="image/*" class="w-full text-sm text-charcoal-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-charcoal-50 file:text-charcoal-700 hover:file:bg-charcoal-100 cursor-pointer">
                <p class="mt-1 text-xs text-charcoal-400">JPG, PNG, WebP up to 2MB.</p>
                @error('avatar') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
            
            <div class="pt-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-rose-500 border-charcoal-300 rounded focus:ring-rose-500" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-charcoal-900">User is Active</span>
                </label>
            </div>
        </div>

        <div class="pt-4 border-t border-charcoal-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-medium text-charcoal-700 bg-white border border-charcoal-200 rounded-xl hover:bg-charcoal-50 transition-colors">Cancel</a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors">Create User</button>
        </div>
    </form>
</x-card>
@endsection
