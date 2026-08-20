@extends('layouts.partials.app')

@section('title', 'CS Dashboard')

@section('page-header', 'Dashboard CS')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name . ' — Ayo buat pelanggan kita tersenyum hari ini!')

@section('page-actions')
    <button type="button" @click="$dispatch('open-followup-modal')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Follow-up
    </button>
    <button type="button" @click="$dispatch('open-create-customer-modal')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Customer
    </button>
@endsection

@section('content')
{{-- KPI Cards with Glassmorphism --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg shadow-emerald-500/30 transform hover:scale-[1.02] transition-transform relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 opacity-10">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="flex flex-col relative z-10">
            <span class="text-emerald-50 text-sm font-medium mb-1">Total Customers</span>
            <span class="text-4xl font-bold font-serif">{{ number_format($totalCustomers) }}</span>
        </div>
    </div>

    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-blue-500/30 transform hover:scale-[1.02] transition-transform relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 opacity-10">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex flex-col relative z-10">
            <span class="text-blue-50 text-sm font-medium mb-1">Active Customers</span>
            <span class="text-4xl font-bold font-serif">{{ number_format($activeCustomers) }}</span>
        </div>
    </div>

    <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg shadow-purple-500/30 transform hover:scale-[1.02] transition-transform relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 opacity-10">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
        </div>
        <div class="flex flex-col relative z-10">
            <span class="text-violet-50 text-sm font-medium mb-1">Pelanggan Baru (Bulan Ini)</span>
            <span class="text-4xl font-bold font-serif">{{ number_format($newCustomersThisMonth) }}</span>
        </div>
    </div>

    <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg shadow-amber-500/30 transform hover:scale-[1.02] transition-transform relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 opacity-10">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="flex flex-col relative z-10">
            <span class="text-amber-50 text-sm font-medium mb-1">Tugas Follow-up Hari Ini</span>
            <span class="text-4xl font-bold font-serif">{{ $todayFollowUps->count() }}</span>
        </div>
    </div>
</div>

<div x-data="{ leftHeight: 'auto', updateHeight() { this.leftHeight = $refs.leftCol.offsetHeight } }" 
     x-init="setTimeout(() => updateHeight(), 100)" 
     @resize.window="updateHeight()" 
     class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
     
    {{-- Main Follow-up Section (2/3 width) --}}
    <div x-ref="leftCol" class="lg:col-span-2 space-y-8">
        
        {{-- Overdue Follow-ups (Peringatan Kritis) --}}
        @if($overdueFollowUps->count() > 0)
        <div x-data="{ showMessageModal: false, selectedCustomer: null, messageText: '', channel: 'whatsapp' }" class="bg-rose-50 rounded-2xl border border-rose-200 overflow-hidden shadow-sm relative">
            <div class="bg-rose-100/50 px-6 py-4 border-b border-rose-200 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </span>
                    <h3 class="font-serif text-lg font-bold text-rose-800">Overdue Follow-ups ({{ $overdueFollowUps->count() }})</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($overdueFollowUps->take(5) as $overdue)
                        <div class="flex items-center justify-between p-4 rounded-xl bg-white border border-rose-100 shadow-sm hover:shadow-md transition-shadow">
                            <div>
                                <p class="text-sm font-bold text-charcoal-900">{{ $overdue->activitable?->name ?? 'Customer' }}</p>
                                <p class="text-xs text-rose-600 mt-1 font-medium">Jatuh tempo: {{ $overdue->follow_up_date->format('d M Y') }}</p>
                            </div>
                            <button type="button" @click="selectedCustomer = { id: {{ $overdue->activitable_id ?? 0 }}, name: '{{ addslashes($overdue->activitable?->name ?? 'Customer') }}' }; showMessageModal = true" class="px-3 py-1 bg-rose-100 text-rose-700 hover:bg-rose-600 hover:text-white rounded-lg text-xs font-bold transition-colors">
                                Hubungi Sekarang
                            </button>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('cs.follow-ups.index') }}" class="text-rose-600 hover:text-rose-700 text-sm font-semibold">Lihat Semua Keterlambatan →</a>
                </div>
            </div>

            {{-- Quick Message Modal --}}
            <div x-show="showMessageModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div x-show="showMessageModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-charcoal-900/40 backdrop-blur-sm" @click="showMessageModal = false"></div>
                    <div x-show="showMessageModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <div class="absolute top-0 right-0 pt-4 pr-4">
                            <button type="button" @click="showMessageModal = false" class="text-charcoal-400 bg-white rounded-md hover:text-charcoal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500" title="Tutup">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="sm:flex sm:items-start">
                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-rose-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg font-medium leading-6 text-charcoal-900 font-serif" id="modal-title">Hubungi Cepat</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-charcoal-500">Kirim pesan follow-up langsung ke <strong x-text="selectedCustomer?.name" class="text-charcoal-900"></strong>.</p>
                                </div>
                                <form action="{{ route('cs.customers.blast') }}" method="POST" class="mt-4 space-y-4">
                                    @csrf
                                    <template x-if="selectedCustomer">
                                        <input type="hidden" name="customer_ids[]" :value="selectedCustomer.id">
                                    </template>
                                    <div>
                                        <label class="block text-sm font-medium text-charcoal-700 mb-1">Pilih Saluran</label>
                                        <div class="flex gap-4">
                                            <label class="flex items-center gap-2 p-3 border border-charcoal-200 rounded-xl cursor-pointer hover:bg-charcoal-50 flex-1" :class="channel === 'whatsapp' ? 'border-emerald-500 bg-emerald-50/50 ring-1 ring-emerald-500' : ''">
                                                <input type="radio" x-model="channel" name="channel" value="whatsapp" class="hidden">
                                                <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" /><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" /></svg>
                                                <span class="text-sm font-medium text-charcoal-900">WhatsApp</span>
                                            </label>
                                            <label class="flex items-center gap-2 p-3 border border-charcoal-200 rounded-xl cursor-pointer hover:bg-charcoal-50 flex-1" :class="channel === 'email' ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500' : ''">
                                                <input type="radio" x-model="channel" name="channel" value="email" class="hidden">
                                                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" /><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" /></svg>
                                                <span class="text-sm font-medium text-charcoal-900">Email</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="message" class="block text-sm font-medium text-charcoal-700 mb-1">Isi Pesan</label>
                                        <textarea x-model="messageText" name="message" id="message" rows="4" class="block w-full border-charcoal-200 rounded-xl shadow-sm focus:ring-rose-500 focus:border-rose-500 sm:text-sm p-3 border" placeholder="Halo, kami dari Beauty Clinic..."></textarea>
                                    </div>
                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                        <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white border border-transparent rounded-xl shadow-sm bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 sm:ml-3 sm:w-auto sm:text-sm">
                                            Kirim Pesan
                                        </button>
                                        <button type="button" @click="showMessageModal = false" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium bg-white border rounded-xl shadow-sm border-charcoal-200 text-charcoal-700 hover:bg-charcoal-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 sm:mt-0 sm:w-auto sm:text-sm">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Alpine Tabs for Follow-ups --}}
        <div x-data="{ tab: 'today' }" @click="setTimeout(() => updateHeight(), 300)" class="bg-white rounded-2xl border border-charcoal-100 shadow-sm overflow-hidden">
            <div class="border-b border-charcoal-100 flex overflow-x-auto">
                <button @click="tab = 'today'" :class="tab === 'today' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-charcoal-500 hover:text-charcoal-700'" class="flex-1 px-6 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    Follow-up Hari Ini
                    <span class="ml-2 bg-charcoal-100 text-charcoal-600 px-2 py-0.5 rounded-full text-xs">{{ $todayFollowUps->count() }}</span>
                </button>
                <button @click="tab = 'upcoming'" :class="tab === 'upcoming' ? 'border-blue-500 text-blue-600' : 'border-transparent text-charcoal-500 hover:text-charcoal-700'" class="flex-1 px-6 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    Jadwal Mendatang
                    <span class="ml-2 bg-charcoal-100 text-charcoal-600 px-2 py-0.5 rounded-full text-xs">{{ $upcomingFollowUps->count() }}</span>
                </button>
            </div>

            <div class="p-6">
                {{-- Tab: Today --}}
                <div x-show="tab === 'today'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    @if($todayFollowUps->count() > 0)
                        <div class="space-y-4">
                            @foreach($todayFollowUps as $fu)
                                <div class="flex items-center gap-4 p-4 rounded-xl bg-charcoal-50/50 hover:bg-emerald-50/50 border border-charcoal-100 hover:border-emerald-100 transition-colors group">
                                    <div class="w-12 h-12 rounded-full bg-white shadow-sm border border-charcoal-100 flex items-center justify-center flex-shrink-0 group-hover:border-emerald-200 group-hover:text-emerald-600 transition-colors">
                                        @if($fu->follow_up_type === 'call')
                                            <svg class="w-5 h-5 text-charcoal-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        @else
                                            <svg class="w-5 h-5 text-charcoal-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-charcoal-900 truncate">
                                            {{ $fu->activitable?->name ?? 'Customer' }}
                                        </p>
                                        <p class="text-xs text-charcoal-500 mt-0.5">{{ $fu->follow_up_notes ?? $fu->subject ?? 'Follow-up' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-4xl">🎉</span>
                            </div>
                            <h3 class="text-lg font-medium text-charcoal-900">Wah, Kosong!</h3>
                            <p class="text-charcoal-500 text-sm mt-1">Tidak ada tugas follow-up hari ini.</p>
                        </div>
                    @endif
                </div>

                {{-- Tab: Upcoming --}}
                <div x-show="tab === 'upcoming'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    @if($upcomingFollowUps->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingFollowUps as $fu)
                                <div class="flex items-center gap-4 p-4 rounded-xl bg-charcoal-50/50 border border-charcoal-100 group">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-charcoal-900 truncate">
                                            {{ $fu->activitable?->name ?? 'Customer' }}
                                        </p>
                                        <p class="text-xs text-blue-600 mt-1 font-medium">{{ $fu->follow_up_date->format('d M Y') }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <x-badge color="blue" size="xs">{{ ucfirst($fu->follow_up_type ?? 'call') }}</x-badge>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">📅</div>
                            <p class="text-charcoal-500 text-sm">Tidak ada jadwal mendatang.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Side Section (1/3 width) --}}
    <div class="space-y-8" :style="leftHeight !== 'auto' ? `height: ${leftHeight}px` : ''">
        {{-- Riwayat Kontak Hari Ini --}}
        <div class="bg-white rounded-2xl border border-charcoal-100 shadow-sm flex flex-col h-full min-h-[300px]">
            <div class="px-6 py-4 border-b border-charcoal-100 flex justify-between items-center bg-charcoal-50/50 shrink-0">
                <h3 class="font-serif text-lg font-bold text-charcoal-900">Kontak Hari Ini</h3>
                <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-bold">{{ $contactedToday->count() }}</span>
            </div>
            <div class="p-6 flex-1 overflow-y-auto custom-scrollbar">
                @if($contactedToday->count() > 0)
                    <div class="space-y-3">
                        @foreach($contactedToday as $activity)
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-charcoal-50 transition-colors group border border-transparent hover:border-charcoal-100">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 
                                    {{ $activity->type === 'whatsapp' ? 'bg-emerald-100 text-emerald-600' : 
                                       ($activity->type === 'email' ? 'bg-blue-100 text-blue-600' : 'bg-charcoal-100 text-charcoal-600') }}">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        @if($activity->type === 'whatsapp')
                                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                                        @elseif($activity->type === 'email')
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                        @else
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0 pt-0.5">
                                    <div class="flex items-center justify-between gap-2 mb-0.5">
                                        <p class="text-sm font-bold text-charcoal-900 truncate">
                                            {{ $activity->activitable?->name ?? 'Customer' }}
                                        </p>
                                        <time class="text-[10px] font-mono text-charcoal-400 whitespace-nowrap">{{ $activity->created_at->format('H:i') }}</time>
                                    </div>
                                    <p class="text-xs text-charcoal-500 truncate group-hover:text-charcoal-700 transition-colors">{{ $activity->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-charcoal-50 rounded-full flex items-center justify-center mx-auto mb-3 text-charcoal-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-charcoal-500 text-sm">Belum ada aktivitas kontak hari ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Create Follow-up Modal --}}
<div x-data="{ showCreateModal: false }" @open-followup-modal.window="showCreateModal = true">
    <div x-show="showCreateModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="fixed inset-0 bg-black/40" @click="showCreateModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md z-10" @click.stop>
            <div class="p-6 border-b border-charcoal-100 flex justify-between items-center">
                <h3 class="font-serif text-xl font-semibold text-charcoal-900">Buat Follow-up</h3>
                <button type="button" @click="showCreateModal = false" class="text-charcoal-400 bg-white rounded-md hover:text-charcoal-500 focus:outline-none" title="Tutup">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
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

{{-- Tambah Customer Modal --}}
<div x-data="{ showCreateCustomerModal: false }" @open-create-customer-modal.window="showCreateCustomerModal = true">
    <div x-show="showCreateCustomerModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="fixed inset-0 bg-black/40" @click="showCreateCustomerModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl z-10 my-8 max-h-[90vh] flex flex-col" @click.stop>
            <div class="p-6 border-b border-charcoal-100 shrink-0 flex justify-between items-center bg-white rounded-t-2xl sticky top-0 z-20">
                <div>
                    <h3 class="font-serif text-xl font-semibold text-charcoal-900">Tambah Customer Baru</h3>
                    <p class="text-charcoal-500 text-sm mt-1">Buat profil pelanggan dengan cepat.</p>
                </div>
                <button type="button" @click="showCreateCustomerModal = false" class="text-charcoal-400 bg-white rounded-md hover:text-charcoal-500 focus:outline-none" title="Tutup">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('cs.customers.store') }}" class="flex flex-col overflow-hidden">
                @csrf
                <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-charcoal-700 mb-1">Nama <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required
                                class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal-700 mb-1">Telepon <span class="text-rose-500">*</span></label>
                            <input type="text" name="phone" required
                                class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-charcoal-700 mb-1">Email</label>
                            <input type="email" name="email"
                                class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal-700 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-charcoal-700 mb-1">Alamat</label>
                        <textarea name="address" rows="2"
                            class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-charcoal-700 mb-1">CS PIC</label>
                            <select name="user_id" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                                @foreach($csUsers as $cs)
                                    <option value="{{ $cs->id }}" {{ auth()->id() == $cs->id ? 'selected' : '' }}>{{ $cs->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal-700 mb-1">Tags <span class="text-charcoal-400 font-normal text-[10px]">(koma)</span></label>
                            <input type="text" name="tags" placeholder="vip, loyal, new"
                                class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-charcoal-700 mb-1">Catatan</label>
                        <textarea name="notes" rows="2"
                            class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>
                </div>
                
                <div class="p-6 border-t border-charcoal-100 shrink-0 bg-charcoal-50 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" @click="showCreateCustomerModal = false" class="px-5 py-2 text-charcoal-600 bg-white border border-charcoal-200 rounded-xl text-sm font-medium hover:bg-charcoal-100 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">Simpan Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
