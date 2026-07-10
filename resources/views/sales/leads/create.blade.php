@extends('layouts.partials.app')

@section('title', 'Tambah Lead')

@section('breadcrumb')
    <li><a href="{{ route('sales.dashboard') }}" class="hover:text-charcoal-700">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('sales.leads.index') }}" class="hover:text-charcoal-700">Leads</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-600 font-medium">Tambah Lead</li>
@endsection

@section('page-header', 'Tambah Lead Baru')
@section('page-subtitle', 'Masukkan data lead baru ke dalam sistem')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <x-card>
        <form action="{{ route('sales.leads.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Lead Name --}}
            <div>
                <label for="lead-name" class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Nama Lengkap *</label>
                <input type="text" id="lead-name" name="name"
                       value="{{ old('name') }}"
                       required
                       class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 transition-all"
                       placeholder="Contoh: John Doe">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Phone --}}
                <div>
                    <label for="lead-phone" class="block text-xs font-mono text-charcoal-400 uppercase mb-1">No. Handphone *</label>
                    <input type="text" id="lead-phone" name="phone"
                           value="{{ old('phone') }}"
                           required
                           class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 transition-all"
                           placeholder="0812...">
                    @error('phone')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="lead-email" class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Email</label>
                    <input type="email" id="lead-email" name="email"
                           value="{{ old('email') }}"
                           class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 transition-all"
                           placeholder="email@example.com">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Lead Source --}}
            <div>
                <label for="lead-source" class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Sumber Lead</label>
                <select id="lead-source" name="lead_source_id"
                        class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 bg-white transition-all">
                    <option value="">Pilih Sumber...</option>
                    @foreach($sources as $source)
                        <option value="{{ $source->id }}" {{ old('lead_source_id') == $source->id ? 'selected' : '' }}>
                            {{ $source->name }}
                        </option>
                    @endforeach
                </select>
                @error('lead_source_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Address --}}
            <div>
                <label for="lead-address" class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Alamat Lengkap</label>
                <textarea id="lead-address" name="address" rows="2"
                          class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 transition-all resize-none"
                          placeholder="Alamat domisili...">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label for="lead-notes" class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Catatan Tambahan</label>
                <textarea id="lead-notes" name="notes" rows="3"
                          class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 transition-all resize-none"
                          placeholder="Keterangan lain tentang lead ini...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-charcoal-100">
                <a href="{{ route('sales.leads.index') }}" class="text-sm text-charcoal-600 hover:text-charcoal-800 transition-colors">← Kembali ke List</a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Simpan Lead
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
