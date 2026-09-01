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

        <button x-show="selectedIds.length > 0" x-cloak @click="showBlastModal = true; $nextTick(() => { if (window.initQuillEditor && $refs.csEditor) window.initQuillEditor($refs.csEditor, 'Tulis pesan blast di sini (bisa format tebal, miring, list nomor/bullet, warna, dll)...'); })"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2 whitespace-nowrap">
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
        {{-- Backdrop --}}
        <div x-show="showBlastModal" 
             x-transition.opacity 
             class="fixed inset-0 bg-charcoal-900/60 backdrop-blur-xs transition-opacity" 
             @click="showBlastModal = false">
        </div>

        {{-- Modal Box Container --}}
        <div class="fixed inset-0 z-10 flex min-h-full items-center justify-center p-4 sm:p-6 overflow-y-auto pointer-events-none">
            <div x-show="showBlastModal" 
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative w-full max-w-xl bg-white rounded-2xl text-left shadow-2xl overflow-hidden pointer-events-auto border border-charcoal-200 my-8" 
                 @click.stop>
                <form id="cs-blast-form" method="POST" action="{{ route('cs.customers.blast') }}"
                    enctype="multipart/form-data"
                    @submit.prevent="
                        const quill = $refs.csEditor ? $refs.csEditor.__quill : null;
                        let html = '';
                        let isBlank = true;
                        if (quill) {
                            html = quill.root.innerHTML;
                            isBlank = quill.getText().trim().length === 0;
                        } else {
                            html = $refs.csEditor ? $refs.csEditor.innerText : '';
                            isBlank = !html.trim();
                        }
                        if (isBlank) {
                            alert('Silakan tulis isi pesan terlebih dahulu.');
                            return;
                        }
                        $refs.csMessageInput.value = html;
                        setTimeout(() => { selectedIds = [] }, 100);
                        $el.submit();
                    ">
                    @csrf
                    <template x-for="id in selectedIds">
                        <input type="hidden" name="customer_ids[]" :value="id">
                    </template>
                    <input type="hidden" name="message" x-ref="csMessageInput">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg leading-6 font-serif font-semibold text-charcoal-900" id="modal-title">Kirim Blast Pesan</h3>
                            <p class="text-sm text-charcoal-500 mt-1">Pesan akan dikirimkan ke <span class="font-bold text-blue-600" x-text="selectedIds.length"></span> pelanggan.</p>
                        </div>
                        <div class="space-y-4">

                            {{-- Jalur Pengiriman --}}
                            <div>
                                <label class="block text-sm font-medium text-charcoal-700 mb-1">Jalur Pengiriman</label>
                                <select name="channel" x-model="channel" class="w-full px-4 py-2 border border-charcoal-200 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="email">Email</option>
                                </select>
                            </div>

                            {{-- Isi Pesan (Rich Text) --}}
                            <div>
                                <label class="block text-sm font-medium text-charcoal-700 mb-1">Isi Pesan (Rich Text)</label>
                                <div class="rounded-xl border border-charcoal-200 overflow-hidden bg-white shadow-xs focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                                    <div x-ref="csEditor"
                                         x-init="$watch('showBlastModal', val => { 
                                             if (val) {
                                                 $nextTick(() => {
                                                     setTimeout(() => {
                                                         if (window.initQuillEditor) {
                                                             window.initQuillEditor($refs.csEditor, 'Tulis pesan blast di sini (bisa format tebal, miring, list nomor/bullet, warna, dll)...');
                                                         }
                                                     }, 50);
                                                 });
                                             }
                                         })"
                                         style="height: 160px;"></div>
                                </div>
                                <p class="text-xs text-charcoal-400 mt-1.5 flex items-center gap-1">
                                    <span>💡</span> Format teks (tebal, miring, list, warna) otomatis disesuaikan untuk Email (HTML) &amp; WhatsApp.
                                </p>
                            </div>

                            {{-- Upload Gambar (Opsional) --}}
                            <div x-data="{ fileName: '', previewUrl: '' }">
                                <label class="block text-sm font-medium text-charcoal-700 mb-1">
                                    Upload Gambar
                                    <span class="text-charcoal-400 font-normal">(Opsional)</span>
                                </label>

                                {{-- Drop Zone --}}
                                <label for="cs-image-upload"
                                    class="flex flex-col items-center justify-center w-full border-2 border-dashed border-charcoal-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-colors p-4"
                                    :class="previewUrl ? 'border-blue-400 bg-blue-50/30' : ''">

                                    {{-- Preview --}}
                                    <template x-if="previewUrl">
                                        <div class="w-full">
                                            <img :src="previewUrl" class="max-h-32 mx-auto rounded-lg object-contain mb-2">
                                            <p class="text-xs text-center text-charcoal-500 truncate" x-text="fileName"></p>
                                            <p class="text-xs text-center text-blue-600 mt-1">Klik untuk ganti gambar</p>
                                        </div>
                                    </template>

                                    {{-- Default State --}}
                                    <template x-if="!previewUrl">
                                        <div class="text-center">
                                            <svg class="w-8 h-8 mx-auto text-charcoal-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-sm text-charcoal-500">Klik untuk pilih gambar</p>
                                            <p class="text-xs text-charcoal-400 mt-1">JPG, PNG, WebP — Maks. 5MB</p>
                                        </div>
                                    </template>

                                    <input id="cs-image-upload" type="file" name="image" accept="image/*" class="hidden"
                                        @change="
                                            const file = $event.target.files[0];
                                            if (file) {
                                                fileName = file.name;
                                                const reader = new FileReader();
                                                reader.onload = e => previewUrl = e.target.result;
                                                reader.readAsDataURL(file);
                                            }
                                        ">
                                </label>

                                <p class="text-xs text-charcoal-400 mt-1">
                                    <span x-show="channel === 'whatsapp'">📲 Gambar akan dikirim langsung ke WhatsApp penerima.</span>
                                    <span x-show="channel === 'email'">📧 Gambar akan tampil di dalam body email.</span>
                                </p>
                            </div>

                        </div>
                    </div>
                    <div class="bg-charcoal-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-charcoal-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
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
