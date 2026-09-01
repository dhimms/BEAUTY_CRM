@extends('layouts.partials.app')

@section('title', 'My Deals')

@section('breadcrumb')
    <li><a href="{{ route('sales.dashboard') }}" class="hover:text-charcoal-700">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-600 font-medium">Deals</li>
@endsection

@section('page-header', 'My Deals')
@section('page-subtitle', 'Daftar semua deal Anda')

@section('page-actions')
    <a href="{{ route('sales.deals.pipeline') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
        Pipeline View
    </a>
@endsection

@section('content')
<x-card :padding="false" x-data="{ 
    selectedDeals: $persist([]), 
    showBlastModal: false,
    channel: 'whatsapp',
    toggleAll(e) {
        if (e.target.checked) {
            this.selectedDeals = Array.from(document.querySelectorAll('.deal-checkbox')).map(cb => cb.value);
        } else {
            this.selectedDeals = [];
        }
    }
}">
    {{-- Filters & Blast Button --}}
    <div class="p-4 border-b border-charcoal-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('sales.deals.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama deal atau lead..."
                       class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 transition-all bg-white">
            </div>
            
            <select name="stage" class="px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300">
                <option value="">Semua Stage</option>
                @foreach($stages as $stage)
                    <option value="{{ $stage->id }}" {{ request('stage') == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
                @endforeach
            </select>
            
            <button type="submit" class="px-4 py-2.5 bg-charcoal-800 text-white rounded-xl text-sm font-medium hover:bg-charcoal-900 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'stage']))
                <a href="{{ route('sales.deals.index') }}" class="px-4 py-2.5 text-charcoal-500 hover:text-charcoal-700 text-sm">Reset</a>
            @endif
        </form>

        <button x-show="selectedDeals.length > 0" x-cloak @click="showBlastModal = true"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Blast Pesan (<span x-text="selectedDeals.length"></span>)
        </button>
    </div>

    {{-- Deals Table --}}
    <div class="overflow-x-auto">
            <table class="w-full text-sm" id="deals-table">
                <thead>
                    <tr class="border-b border-charcoal-200 bg-charcoal-50/50">
                        <th class="px-6 py-3 text-left w-12">
                            <input type="checkbox" @change="toggleAll" class="rounded border-charcoal-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider">Deal</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider hidden sm:table-cell">Lead</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider hidden md:table-cell">Stage</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider hidden lg:table-cell">Terakhir Dihubungi</th>
                        <th class="text-left px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider hidden lg:table-cell">Expected Close</th>
                        <th class="text-right px-6 py-3 text-xs font-mono text-charcoal-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal-100">
                    @forelse($deals as $deal)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <input type="checkbox" value="{{ $deal->id }}" x-model="selectedDeals" class="deal-checkbox rounded border-charcoal-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('sales.deals.show', $deal) }}" class="text-charcoal-800 font-medium hover:text-blue-600 transition-colors">
                                    {{ $deal->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                @if($deal->lead)
                                    <a href="{{ route('sales.leads.show', $deal->lead) }}" class="text-xs text-blue-600 hover:text-blue-700">{{ $deal->lead->name }}</a>
                                @else
                                    <span class="text-xs text-charcoal-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                @if($deal->pipelineStage)
                                    <span class="inline-flex items-center gap-1.5 text-xs">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $deal->pipelineStage->color }}"></span>
                                        {{ $deal->pipelineStage->name }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :color="$deal->status_color" size="xs">{{ ucfirst($deal->status) }}</x-badge>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell text-xs text-charcoal-500">
                                @if($deal->lead && $deal->lead->latestActivity)
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-{{ $deal->lead->latestActivity->type_color }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span title="{{ $deal->lead->latestActivity->description }}">
                                            {{ $deal->lead->latestActivity->activity_date->diffForHumans() }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-charcoal-400 italic">Belum ada aktivitas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <span class="text-xs font-mono text-charcoal-500">
                                    {{ $deal->expected_close_date ? $deal->expected_close_date->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('sales.deals.show', $deal) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-charcoal-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                                </svg>
                                <p class="text-charcoal-500 text-sm">Belum ada deal.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
    
    @if($deals->hasPages())
        <div class="px-6 py-4 border-t border-charcoal-100">
            {{ $deals->links() }}
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
                <form id="sales-blast-form" method="POST" action="{{ route('sales.deals.blast') }}"
                    enctype="multipart/form-data"
                    @submit.prevent="
                        const quill = $refs.salesEditor ? $refs.salesEditor.__quill : null;
                        let html = '';
                        let isBlank = true;
                        if (quill) {
                            html = quill.root.innerHTML;
                            isBlank = quill.getText().trim().length === 0;
                        } else {
                            html = $refs.salesEditor ? $refs.salesEditor.innerText : '';
                            isBlank = !html.trim();
                        }
                        if (isBlank) {
                            alert('Silakan tulis isi pesan terlebih dahulu.');
                            return;
                        }
                        $refs.salesMessageInput.value = html;
                        setTimeout(() => { selectedDeals = [] }, 100);
                        $el.submit();
                    ">
                    @csrf
                    <template x-for="id in selectedDeals">
                        <input type="hidden" name="deal_ids[]" :value="id">
                    </template>
                    <input type="hidden" name="message" x-ref="salesMessageInput">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg leading-6 font-serif font-semibold text-charcoal-900" id="modal-title">Kirim Blast Pesan</h3>
                            <p class="text-sm text-charcoal-500 mt-1">Pesan akan dikirimkan ke <span class="font-bold text-blue-600" x-text="selectedDeals.length"></span> lead.</p>
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
                                    <div x-ref="salesEditor"
                                         x-init="$watch('showBlastModal', val => { 
                                             if (val) {
                                                 $nextTick(() => {
                                                     if (window.initQuillEditor) {
                                                         window.initQuillEditor($refs.salesEditor, 'Tulis pesan blast di sini (bisa format tebal, miring, list nomor/bullet, warna, dll)...');
                                                     }
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
                                <label for="sales-image-upload"
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

                                    <input id="sales-image-upload" type="file" name="image" accept="image/*" class="hidden"
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

