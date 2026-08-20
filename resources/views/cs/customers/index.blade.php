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
<x-card :padding="false" x-data="{ 
    selectedIds: $persist([]), 
    showBlastModal: false,
    channel: 'whatsapp',
    message: '',
    toggleAll(e) {
        if (e.target.checked) {
            this.selectedIds = Array.from(document.querySelectorAll('.customer-checkbox')).map(cb => cb.value);
        } else {
            this.selectedIds = [];
        }
    }
}">
    {{-- Filters & Blast Button --}}
    <div class="p-4 border-b border-charcoal-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('cs.customers.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, telepon..."
                    class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
            </div>
            
            <input type="number" name="min_spend" value="{{ request('min_spend') }}" placeholder="Min Belanja (Rp)"
                class="px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 bg-white w-40">
                
            <input type="text" name="deal_keyword" value="{{ request('deal_keyword') }}" placeholder="Pernah Beli..."
                class="px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 bg-white w-40">

            <select name="status" class="px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-charcoal-800 text-white rounded-xl text-sm font-medium hover:bg-charcoal-900 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'user_id', 'min_spend', 'deal_keyword']))
                <a href="{{ route('cs.customers.index') }}" class="px-4 py-2.5 text-charcoal-500 hover:text-charcoal-700 text-sm">Reset</a>
            @endif
        </form>

        <button x-show="selectedIds.length > 0" x-cloak @click="showBlastModal = true"
            class="px-4 py-2.5 bg-amber-600 text-white rounded-xl text-sm font-medium hover:bg-amber-700 transition-colors shadow-sm flex items-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Blast Pesan (<span x-text="selectedIds.length"></span>)
        </button>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left w-12">
                        <input type="checkbox" @change="toggleAll" class="rounded border-charcoal-300 text-emerald-600 focus:ring-emerald-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Nama & Kontak</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Total Belanja</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Dihubungi</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-charcoal-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $customer->id }}" x-model="selectedIds" class="customer-checkbox rounded border-charcoal-300 text-emerald-600 focus:ring-emerald-500">
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('cs.customers.show', $customer) }}" class="font-semibold text-charcoal-900 hover:text-emerald-600 transition-colors block">
                                {{ $customer->name }}
                            </a>
                            <div class="text-xs text-charcoal-500 mt-1">{{ $customer->phone }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :color="$customer->status === 'active' ? 'emerald' : 'gray'" size="xs">
                                {{ ucfirst($customer->status) }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm text-charcoal-700">
                            Rp {{ number_format($customer->total_spend, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-charcoal-500 text-xs">
                            @if($customer->last_contacted_at)
                                {{ \Carbon\Carbon::parse($customer->last_contacted_at)->diffForHumans() }}
                            @else
                                <span class="text-charcoal-300 italic">Belum pernah</span>
                            @endif
                        </td>
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

    {{-- Blast Message Modal --}}
    <div x-show="showBlastModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showBlastModal" x-transition.opacity class="fixed inset-0 bg-charcoal-900/50 backdrop-blur-sm transition-opacity" @click="showBlastModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showBlastModal" x-transition class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" @click.stop>
                <form method="POST" action="{{ route('cs.customers.blast') }}" @submit="setTimeout(() => { selectedIds = [] }, 100)">
                    @csrf
                    <template x-for="id in selectedIds">
                        <input type="hidden" name="customer_ids[]" :value="id">
                    </template>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg leading-6 font-serif font-semibold text-charcoal-900" id="modal-title">Kirim Blast Pesan</h3>
                            <p class="text-sm text-charcoal-500 mt-1">Pesan akan dikirimkan ke <span class="font-bold text-amber-600" x-text="selectedIds.length"></span> pelanggan.</p>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-charcoal-700 mb-1">Jalur Pengiriman</label>
                                <select name="channel" x-model="channel" class="w-full px-4 py-2 border border-charcoal-200 rounded-xl focus:ring-2 focus:ring-amber-500 bg-white">
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="email">Email</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-charcoal-700 mb-1">Isi Pesan</label>
                                <textarea name="message" x-model="message" rows="4" required placeholder="Ketik pesan Anda di sini..."
                                    class="w-full px-4 py-3 border border-charcoal-200 rounded-xl focus:ring-2 focus:ring-amber-500"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-charcoal-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-charcoal-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Kirim Pesan
                        </button>
                        <button type="button" @click="showBlastModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-charcoal-200 shadow-sm px-4 py-2 bg-white text-base font-medium text-charcoal-700 hover:bg-charcoal-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-card>
@endsection
