@extends('layouts.partials.app')

@section('title', 'My Profile')

@section('breadcrumb')
    <li><span class="text-charcoal-400">Settings</span></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">My Profile</li>
@endsection

@section('page-header', 'My Profile')
@section('page-subtitle', 'Kelola informasi pribadi dan pengaturan akun Anda')

@section('content')
<div class="max-w-3xl">
    <x-card>
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Avatar Section --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 pb-6 border-b border-charcoal-100">
                <div class="relative group">
                    <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Profile Photo" 
                         class="w-24 h-24 rounded-2xl object-cover ring-4 ring-charcoal-50 shadow-sm transition-all group-hover:ring-charcoal-100">
                    <div class="absolute inset-0 bg-black/40 rounded-2xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-charcoal-900 mb-1">Foto Profil</h3>
                    <p class="text-xs text-charcoal-500 mb-3">Format JPG, PNG atau GIF (Maks. 2MB)</p>
                    <div class="relative">
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               onchange="document.getElementById('avatar-preview').src = window.URL.createObjectURL(this.files[0])">
                        <button type="button" class="px-4 py-2 bg-white border border-charcoal-200 text-charcoal-700 rounded-xl text-sm font-medium hover:bg-charcoal-50 transition-colors">
                            Ubah Foto
                        </button>
                    </div>
                    @error('avatar') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Basic Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-xs font-mono text-charcoal-400 uppercase">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 bg-white border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-300 transition-all">
                    @error('name') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-mono text-charcoal-400 uppercase">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 bg-white border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-300 transition-all">
                    @error('email') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-mono text-charcoal-400 uppercase">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2.5 bg-white border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-300 transition-all">
                    @error('phone') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-mono text-charcoal-400 uppercase">Role (Hak Akses)</label>
                    <input type="text" value="{{ $user->getRoleNames()->first() }}" disabled
                        class="w-full px-4 py-2.5 bg-charcoal-50 text-charcoal-500 border border-charcoal-100 rounded-xl text-sm cursor-not-allowed">
                </div>
            </div>

            <div class="flex justify-end pt-6 mt-6 border-t border-charcoal-100">
                <button type="submit" class="px-6 py-2.5 bg-rose-600 text-white text-sm font-medium rounded-xl hover:bg-rose-700 shadow-sm shadow-rose-600/20 transition-all active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
