@extends('layouts.partials.app')

@section('title', 'Customers')

@section('breadcrumb')
    <li><a href="{{ route('cs.dashboard') }}" class="hover:text-emerald-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Customers</li>
@endsection

@section('page-header', 'Customers')
@section('page-subtitle', 'Kelola data customer Anda')

@section('page-actions')
    <a href="{{ route('cs.customers.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Customer
    </a>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Filters --}}
    <div class="p-4 border-b border-charcoal-100" x-data="{ showFilters: false }">
        <form method="GET" action="{{ route('cs.customers.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau telepon..."
                    class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
            </div>
            <select name="status" class="px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <select name="user_id" class="px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua CS PIC</option>
                @foreach($csUsers as $cs)
                    <option value="{{ $cs->id }}" {{ request('user_id') == $cs->id ? 'selected' : '' }}>{{ $cs->name }}</option>
                @endforeach
            </select>
            <button type="submit"
                class="px-4 py-2.5 bg-charcoal-800 text-white rounded-xl text-sm font-medium hover:bg-charcoal-900 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'user_id']))
                <a href="{{ route('cs.customers.index') }}" class="px-4 py-2.5 text-charcoal-500 hover:text-charcoal-700 text-sm">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">CS PIC</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Dibuat</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-charcoal-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('cs.customers.show', $customer) }}" class="font-semibold text-charcoal-900 hover:text-emerald-600 transition-colors">
                                {{ $customer->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-charcoal-600">{{ $customer->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-charcoal-600 font-mono text-xs">{{ $customer->phone }}</td>
                        <td class="px-6 py-4">
                            <x-badge :color="$customer->status === 'active' ? 'emerald' : 'gray'" size="xs">
                                {{ ucfirst($customer->status) }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-charcoal-600">{{ $customer->csUser?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-charcoal-500 text-xs">{{ $customer->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('cs.customers.show', $customer) }}"
                                class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-charcoal-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-charcoal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p>Belum ada data customer.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($customers->hasPages())
        <div class="p-4 border-t border-charcoal-100">
            {{ $customers->links() }}
        </div>
    @endif
</x-card>
@endsection
