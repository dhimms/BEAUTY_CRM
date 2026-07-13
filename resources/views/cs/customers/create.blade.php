@extends('layouts.partials.app')

@section('title', 'Tambah Customer')

@section('breadcrumb')
    <li><a href="{{ route('cs.dashboard') }}" class="hover:text-emerald-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('cs.customers.index') }}" class="hover:text-emerald-600">Customers</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Tambah</li>
@endsection

@section('page-header', 'Tambah Customer')
@section('page-subtitle', 'Buat data customer baru')

@section('content')
<div class="max-w-2xl">
    <x-card>
        <form method="POST" action="{{ route('cs.customers.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Nama <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Telepon <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Alamat</label>
                <textarea name="address" rows="3"
                    class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('address') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Status</label>
                    <select name="status"
                        class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1.5">CS PIC</label>
                    <select name="user_id"
                        class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                        @foreach($csUsers as $cs)
                            <option value="{{ $cs->id }}" {{ old('user_id', auth()->id()) == $cs->id ? 'selected' : '' }}>{{ $cs->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Tags <span class="text-charcoal-400 text-xs">(pisahkan dengan koma)</span></label>
                <input type="text" name="tags" value="{{ old('tags') }}" placeholder="vip, loyal, new"
                    class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-charcoal-100">
                <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">
                    Simpan Customer
                </button>
                <a href="{{ route('cs.customers.index') }}"
                    class="px-6 py-2.5 text-charcoal-600 hover:text-charcoal-800 text-sm font-medium">
                    Batal
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection
