@extends('layouts.partials.app')

@section('title', 'Customers')

@section('breadcrumb')
    <li><a href="{{ route('sales.dashboard') }}" class="hover:text-charcoal-700">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-600 font-medium">Customers</li>
@endsection

@section('page-header', 'Customers')
@section('page-subtitle', 'Daftar pelanggan dari deal yang Anda menangkan')

@section('content')
<div class="space-y-6">

    {{-- Filter Bar --}}
    <x-card :padding="false" class="overflow-hidden">
        <form action="{{ route('sales.customers.index') }}" method="GET" class="flex flex-col sm:flex-row p-4 gap-4 bg-white border-b border-charcoal-100">
            <div class="flex-1 min-w-[200px] relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-charcoal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau no HP..." 
                       class="w-full pl-9 pr-3 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-shadow">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">Filter</button>
                @if(request()->anyFilled(['search']))
                    <a href="{{ route('sales.customers.index') }}" class="px-4 py-2 bg-white border border-charcoal-200 text-charcoal-600 text-sm font-medium rounded-xl hover:bg-charcoal-50 transition-colors">Reset</a>
                @endif
            </div>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-charcoal-50 text-charcoal-500 font-mono text-xs uppercase border-b border-charcoal-100">
                    <tr>
                        <th class="px-6 py-3 font-medium">Nama Pelanggan</th>
                        <th class="px-6 py-3 font-medium">Kontak</th>
                        <th class="px-6 py-3 font-medium">Asal Deal / Lead</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Tanggal Dibuat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal-100 text-charcoal-700">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-charcoal-900">{{ $customer->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="flex items-center gap-1.5 text-xs">
                                        <svg class="w-3.5 h-3.5 text-charcoal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        {{ $customer->phone }}
                                    </span>
                                    @if($customer->email)
                                        <span class="flex items-center gap-1.5 text-xs text-charcoal-500">
                                            <svg class="w-3.5 h-3.5 text-charcoal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            {{ $customer->email }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($customer->lead)
                                    <a href="{{ route('sales.leads.show', $customer->lead) }}" class="text-blue-600 hover:text-blue-700 font-medium hover:underline text-xs">
                                        {{ $customer->lead->name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($customer->status === 'active')
                                    <x-badge color="emerald" size="xs">Aktif</x-badge>
                                @else
                                    <x-badge color="gray" size="xs">Non-aktif</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-mono text-charcoal-500">{{ $customer->created_at->format('d M Y') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-charcoal-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-charcoal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <p>Belum ada data pelanggan</p>
                            </td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-charcoal-100 bg-charcoal-50/30">
                {{ $customers->links() }}
            </div>
        @endif
    </x-card>

</div>
@endsection
