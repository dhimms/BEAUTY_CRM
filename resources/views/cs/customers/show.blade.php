@extends('layouts.partials.app')

@section('title', 'Detail Customer — ' . $customer->name)

@section('breadcrumb')
    <li><a href="{{ route('cs.dashboard') }}" class="hover:text-emerald-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('cs.customers.index') }}" class="hover:text-emerald-600">Customers</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">{{ $customer->name }}</li>
@endsection

@section('content')
<div x-data="{ activeTab: 'history', showEditModal: false, showActivityModal: false }">
    {{-- Customer Header Card --}}
    <div class="bg-white rounded-xl border border-charcoal-200 shadow-sm p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl font-serif font-bold text-emerald-700">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h1 class="font-serif text-2xl font-semibold text-charcoal-900">{{ $customer->name }}</h1>
                    <div class="flex items-center gap-3 mt-1">
                        <x-badge :color="$customer->status === 'active' ? 'emerald' : 'gray'" size="xs">{{ ucfirst($customer->status) }}</x-badge>
                        @if($customer->tags)
                            @foreach($customer->tags as $tag)
                                <span class="text-xs bg-charcoal-100 text-charcoal-600 px-2 py-0.5 rounded-full">{{ $tag }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showActivityModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Log Aktivitas
                </button>
                <button @click="showEditModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-charcoal-200 text-charcoal-700 rounded-xl text-sm font-medium hover:bg-charcoal-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </button>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mb-6">
        <div class="flex gap-1 bg-charcoal-100 rounded-xl p-1 max-w-md">
            <button @click="activeTab = 'history'"
                :class="activeTab === 'history' ? 'bg-white shadow-sm text-charcoal-900' : 'text-charcoal-500 hover:text-charcoal-700'"
                class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all">Service History</button>
            <button @click="activeTab = 'activity'"
                :class="activeTab === 'activity' ? 'bg-white shadow-sm text-charcoal-900' : 'text-charcoal-500 hover:text-charcoal-700'"
                class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all">Activity Log</button>
            <button @click="activeTab = 'info'"
                :class="activeTab === 'info' ? 'bg-white shadow-sm text-charcoal-900' : 'text-charcoal-500 hover:text-charcoal-700'"
                class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all">Info</button>
        </div>
    </div>

    {{-- Tab: Service History --}}
    <div x-show="activeTab === 'history'" x-cloak>
        <x-card :padding="false">
            <div class="p-4 border-b border-charcoal-100">
                <h3 class="font-serif text-lg font-semibold text-charcoal-900">Service Tickets</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-charcoal-50/50">
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">No. Ticket</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">CS</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-charcoal-100">
                        @forelse($customer->serviceTickets as $ticket)
                            <tr class="hover:bg-charcoal-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="{{ route('cs.tickets.show', $ticket) }}" class="font-mono text-emerald-600 hover:text-emerald-700 font-medium">{{ $ticket->ticket_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-charcoal-900 font-medium">{{ $ticket->title }}</td>
                                <td class="px-6 py-4"><x-badge :color="$ticket->priority_color" size="xs">{{ ucfirst($ticket->priority) }}</x-badge></td>
                                <td class="px-6 py-4"><x-badge :color="$ticket->status_color" size="xs">{{ config('beauty-crm.ticket_statuses.' . $ticket->status) }}</x-badge></td>
                                <td class="px-6 py-4 text-charcoal-600 text-xs">{{ $ticket->assignedUser?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-charcoal-500 text-xs">{{ $ticket->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-charcoal-400">Belum ada service ticket.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    {{-- Tab: Activity Log --}}
    <div x-show="activeTab === 'activity'" x-cloak>
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-4">Timeline Aktivitas</h3>
            @if($customer->activities->count() > 0)
                <div class="relative">
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-charcoal-200"></div>
                    <div class="space-y-6">
                        @foreach($customer->activities as $activity)
                            @php
                                $iconColors = [
                                    'call' => 'bg-blue-100 text-blue-600',
                                    'whatsapp' => 'bg-emerald-100 text-emerald-600',
                                    'email' => 'bg-purple-100 text-purple-600',
                                    'meeting' => 'bg-amber-100 text-amber-600',
                                    'note' => 'bg-gray-100 text-gray-600',
                                ];
                                $colorClass = $iconColors[$activity->type] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <div class="relative flex gap-4 pl-2">
                                <div class="w-10 h-10 rounded-full {{ $colorClass }} flex items-center justify-center flex-shrink-0 z-10 border-2 border-white">
                                    @if($activity->type === 'call')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    @elseif($activity->type === 'whatsapp')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    @elseif($activity->type === 'email')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @endif
                                </div>
                                <div class="flex-1 bg-charcoal-50/50 rounded-xl p-4">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-semibold text-charcoal-900">{{ $activity->subject ?? ucfirst($activity->type) }}</span>
                                        <span class="text-xs text-charcoal-400">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($activity->description)
                                        <p class="text-sm text-charcoal-600">{{ $activity->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-3 mt-2 text-xs text-charcoal-400">
                                        <span>oleh {{ $activity->user?->name ?? '-' }}</span>
                                        @if($activity->duration)
                                            <span>• {{ $activity->duration }}</span>
                                        @endif
                                        @if($activity->result)
                                            <span>• {{ ucfirst($activity->result) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-charcoal-400 text-sm">Belum ada aktivitas tercatat.</p>
                </div>
            @endif
        </x-card>
    </div>

    {{-- Tab: Info --}}
    <div x-show="activeTab === 'info'" x-cloak>
        <x-card>
            <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-6">Informasi Customer</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">Nama Lengkap</label>
                        <p class="text-sm text-charcoal-900 font-medium mt-1">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">Email</label>
                        <p class="text-sm text-charcoal-900 mt-1">{{ $customer->email ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">Telepon</label>
                        <p class="text-sm text-charcoal-900 font-mono mt-1">{{ $customer->phone }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">Alamat</label>
                        <p class="text-sm text-charcoal-900 mt-1">{{ $customer->address ?? '-' }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">CS PIC</label>
                        <p class="text-sm text-charcoal-900 mt-1">{{ $customer->csUser?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">Status</label>
                        <p class="mt-1"><x-badge :color="$customer->status === 'active' ? 'emerald' : 'gray'" size="xs">{{ ucfirst($customer->status) }}</x-badge></p>
                    </div>
                    <div>
                        <label class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">Tags</label>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @if($customer->tags)
                                @foreach($customer->tags as $tag)
                                    <span class="text-xs bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200">{{ $tag }}</span>
                                @endforeach
                            @else
                                <span class="text-sm text-charcoal-400">-</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">Catatan</label>
                        <p class="text-sm text-charcoal-900 mt-1">{{ $customer->notes ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">Terdaftar</label>
                        <p class="text-sm text-charcoal-900 mt-1">{{ $customer->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Edit Modal --}}
    <div x-show="showEditModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="fixed inset-0 bg-black/40" @click="showEditModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[80vh] overflow-y-auto z-10" @click.stop>
            <div class="p-6 border-b border-charcoal-100">
                <h3 class="font-serif text-xl font-semibold text-charcoal-900">Edit Customer</h3>
            </div>
            <form method="POST" action="{{ route('cs.customers.update', $customer) }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-charcoal-700 mb-1">Nama</label>
                        <input type="text" name="name" value="{{ $customer->name }}" required class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-charcoal-700 mb-1">Telepon</label>
                        <input type="text" name="phone" value="{{ $customer->phone }}" required class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ $customer->email }}" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Alamat</label>
                    <textarea name="address" rows="2" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ $customer->address }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-charcoal-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                            <option value="active" {{ $customer->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $customer->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-charcoal-700 mb-1">CS PIC</label>
                        <select name="user_id" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                            @foreach($csUsers as $cs)
                                <option value="{{ $cs->id }}" {{ $customer->user_id == $cs->id ? 'selected' : '' }}>{{ $cs->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Tags</label>
                    <input type="text" name="tags" value="{{ is_array($customer->tags) ? implode(', ', $customer->tags) : $customer->tags }}" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ $customer->notes }}</textarea>
                </div>
                <div class="flex items-center gap-3 pt-3 border-t border-charcoal-100">
                    <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700">Simpan</button>
                    <button type="button" @click="showEditModal = false" class="px-5 py-2 text-charcoal-500 text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Log Activity Modal --}}
    <div x-show="showActivityModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="fixed inset-0 bg-black/40" @click="showActivityModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg z-10" @click.stop>
            <div class="p-6 border-b border-charcoal-100">
                <h3 class="font-serif text-xl font-semibold text-charcoal-900">Log Aktivitas</h3>
            </div>
            <form method="POST" action="{{ route('cs.activities.store') }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="activitable_type" value="customer">
                <input type="hidden" name="activitable_id" value="{{ $customer->id }}">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-charcoal-700 mb-1">Tipe</label>
                        <select name="type" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                            @foreach(config('beauty-crm.activity_types') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-charcoal-700 mb-1">Durasi</label>
                        <select name="duration" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                            <option value="">-</option>
                            <option value="5min">5 Menit</option>
                            <option value="15min">15 Menit</option>
                            <option value="30min">30 Menit</option>
                            <option value="1hr">1 Jam</option>
                            <option value="2hr">2 Jam</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Subject</label>
                    <input type="text" name="subject" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Hasil</label>
                    <select name="result" class="w-full px-3 py-2 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                        <option value="">-</option>
                        @foreach(config('beauty-crm.activity_results') as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3 pt-3 border-t border-charcoal-100">
                    <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700">Simpan</button>
                    <button type="button" @click="showActivityModal = false" class="px-5 py-2 text-charcoal-500 text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
