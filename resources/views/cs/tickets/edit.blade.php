@extends('layouts.partials.app')

@section('title', 'Edit Ticket — ' . $ticket->ticket_number)

@section('breadcrumb')
    <li><a href="{{ route('cs.dashboard') }}" class="hover:text-emerald-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('cs.tickets.index') }}" class="hover:text-emerald-600">Tickets</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Edit {{ $ticket->ticket_number }}</li>
@endsection

@section('page-header', 'Edit Ticket')
@section('page-subtitle', $ticket->ticket_number)

@section('content')
<div class="max-w-2xl">
    <x-card>
        <form method="POST" action="{{ route('cs.tickets.update', $ticket) }}" class="space-y-6">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Customer <span class="text-rose-500">*</span></label>
                <select name="customer_id" required class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id', $ticket->customer_id) == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} — {{ $customer->phone }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Judul <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $ticket->title) }}" required
                    class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Kategori</label>
                    <select name="category" class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                        <option value="">Pilih Kategori</option>
                        @foreach(config('beauty-crm.ticket_categories') as $key => $label)
                            <option value="{{ $key }}" {{ old('category', $ticket->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Prioritas <span class="text-rose-500">*</span></label>
                    <select name="priority" required class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                        @foreach(config('beauty-crm.ticket_priorities') as $key => $label)
                            <option value="{{ $key }}" {{ old('priority', $ticket->priority) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Assign CS</label>
                    <select name="assigned_to" class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-500">
                        @foreach($csUsers as $cs)
                            <option value="{{ $cs->id }}" {{ old('assigned_to', $ticket->assigned_to) == $cs->id ? 'selected' : '' }}>{{ $cs->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('description', $ticket->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-charcoal-100">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">Simpan Perubahan</button>
                <a href="{{ route('cs.tickets.show', $ticket) }}" class="px-6 py-2.5 text-charcoal-600 hover:text-charcoal-800 text-sm font-medium">Batal</a>
            </div>
        </form>
    </x-card>
</div>
@endsection
