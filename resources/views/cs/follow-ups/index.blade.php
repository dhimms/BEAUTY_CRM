@extends('layouts.partials.app')

@section('title', 'Follow-ups')

@section('breadcrumb')
    <li><a href="{{ route('cs.dashboard') }}" class="hover:text-emerald-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Follow-ups</li>
@endsection

@section('page-header', 'Follow-up Tasks')
@section('page-subtitle', 'Kelola jadwal follow-up customer')

@section('content')
<div x-data="{ showCreateModal: false }">
    {{-- Actions & Filters --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <form method="GET" action="{{ route('cs.follow-ups.index') }}" class="flex items-center gap-3">
            <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
            <span class="text-charcoal-400 text-sm">—</span>
            <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
            <button type="submit" class="px-4 py-2 bg-charcoal-800 text-white rounded-xl text-sm font-medium hover:bg-charcoal-900">Filter</button>
        </form>
        <button @click="showCreateModal = true"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Follow-up
        </button>
    </div>

    {{-- Overdue Warning --}}
    @if($overdue->count() > 0)
        <div class="mb-6 bg-rose-50 border border-rose-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <h3 class="font-semibold text-rose-800">{{ $overdue->count() }} Follow-up Overdue!</h3>
            </div>
            <div class="space-y-2">
                @foreach($overdue as $item)
                    <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-rose-100">
                        <div>
                            <p class="text-sm font-medium text-charcoal-900">{{ $item->activitable?->name ?? 'Customer' }}</p>
                            <p class="text-xs text-rose-600">Jatuh tempo: {{ $item->follow_up_date->format('d M Y') }} ({{ $item->follow_up_date->diffForHumans() }})</p>
                        </div>
                        <form method="POST" action="{{ route('cs.follow-ups.complete', $item) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-xs font-medium hover:bg-emerald-700">Selesai</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Pending Follow-ups --}}
    <div class="mb-6">
        <x-card :padding="false">
            <div class="p-4 border-b border-charcoal-100">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Pending Follow-ups ({{ $pending->count() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-charcoal-50/50">
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Catatan</th>
                            <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-charcoal-100">
                        @forelse($pending as $fu)
                            <tr class="hover:bg-charcoal-50/30 transition-colors">
                                <td class="px-6 py-4 font-medium text-charcoal-900">{{ $fu->activitable?->name ?? '-' }}</td>
                                <td class="px-6 py-4"><x-badge :color="$fu->type_color" size="xs">{{ ucfirst($fu->follow_up_type ?? $fu->type) }}</x-badge></td>
                                <td class="px-6 py-4 text-charcoal-700 font-mono text-xs">{{ $fu->follow_up_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-charcoal-600 text-xs truncate max-w-[200px]">{{ $fu->follow_up_notes ?? '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('cs.follow-ups.complete', $fu) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-xs font-medium hover:bg-emerald-700">Selesai</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-charcoal-400">Tidak ada follow-up pending.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    {{-- Completed Follow-ups --}}
    <x-card :padding="false">
        <div class="p-4 border-b border-charcoal-100">
            <h3 class="font-serif text-lg font-semibold text-charcoal-900">Completed ({{ $completed->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-charcoal-50/50">
                        <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal-100">
                    @forelse($completed as $fu)
                        <tr class="hover:bg-charcoal-50/30 transition-colors opacity-60">
                            <td class="px-6 py-3 text-charcoal-700">{{ $fu->activitable?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-xs">{{ ucfirst($fu->follow_up_type ?? $fu->type) }}</td>
                            <td class="px-6 py-3 text-charcoal-500 font-mono text-xs">{{ $fu->follow_up_date->format('d M Y') }}</td>
                            <td class="px-6 py-3"><x-badge color="emerald" size="xs">Done</x-badge></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-6 text-center text-charcoal-400">Belum ada follow-up selesai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Create Follow-up Modal --}}
    <div x-show="showCreateModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="fixed inset-0 bg-black/40" @click="showCreateModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md z-10" @click.stop>
            <div class="p-6 border-b border-charcoal-100">
                <h3 class="font-serif text-xl font-semibold text-charcoal-900">Buat Follow-up</h3>
            </div>
            <form method="POST" action="{{ route('cs.follow-ups.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Customer</label>
                    <select name="customer_id" required class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                        <option value="">Pilih Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Tanggal Follow-up</label>
                    <input type="date" name="follow_up_date" required class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Tipe</label>
                    <select name="follow_up_type" required class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                        <option value="call">Telepon</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="email">Email</option>
                        <option value="meeting">Meeting</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Subject</label>
                    <input type="text" name="subject" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Catatan</label>
                    <textarea name="follow_up_notes" rows="3" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>
                <div class="flex items-center gap-3 pt-3 border-t border-charcoal-100">
                    <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700">Simpan</button>
                    <button type="button" @click="showCreateModal = false" class="px-5 py-2 text-charcoal-500 text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
