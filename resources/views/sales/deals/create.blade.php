@extends('layouts.partials.app')

@section('title', 'Create Deal')

@section('breadcrumb')
    <li><a href="{{ route('sales.dashboard') }}" class="hover:text-charcoal-700">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('sales.deals.index') }}" class="hover:text-charcoal-700">Deals</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-600 font-medium">Create Deal</li>
@endsection

@section('page-header', 'Buat Deal Baru')
@section('page-subtitle', 'Konversi lead menjadi deal')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Lead Info (Read Only) --}}
    <x-card>
        <div class="flex items-center gap-4 mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-serif font-bold text-blue-600">{{ strtoupper(substr($lead->name, 0, 1)) }}</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-charcoal-800">{{ $lead->name }}</h3>
                <p class="text-xs text-charcoal-500">{{ $lead->email ?? $lead->phone }}</p>
            </div>
            <div class="ml-auto">
                <x-badge color="emerald" size="xs">Qualified</x-badge>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4 text-center pt-3 border-t border-charcoal-100">
            <div>
                <p class="text-xs font-mono text-charcoal-400 uppercase">Sumber</p>
                <p class="text-sm font-medium text-charcoal-700 mt-0.5">{{ $lead->source->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-mono text-charcoal-400 uppercase">Telepon</p>
                <p class="text-sm font-medium text-charcoal-700 mt-0.5">{{ $lead->phone }}</p>
            </div>
            <div>
                <p class="text-xs font-mono text-charcoal-400 uppercase">Tanggal Masuk</p>
                <p class="text-sm font-medium text-charcoal-700 mt-0.5">{{ $lead->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </x-card>

    {{-- Deal Form --}}
    <x-card>
        <h3 class="font-serif text-lg font-semibold text-charcoal-900 mb-6">Detail Deal</h3>

        <form action="{{ route('sales.deals.store') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="lead_id" value="{{ $lead->id }}">

            {{-- Deal Name --}}
            <div>
                <label for="deal-name" class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Nama Deal *</label>
                <input type="text" id="deal-name" name="name"
                       value="{{ old('name', $lead->name . ' - Deal') }}"
                       required
                       class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 transition-all"
                       placeholder="Nama deal...">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Value --}}
            <div>
                <label for="deal-value" class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Nilai Deal (Rp) *</label>
                <input type="number" id="deal-value" name="value"
                       value="{{ old('value', 0) }}"
                       required min="0" step="1000"
                       class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 transition-all"
                       placeholder="0">
                @error('value')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Pipeline Stage --}}
            <div>
                <label for="deal-stage" class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Pipeline Stage</label>
                <select id="deal-stage" name="pipeline_stage_id"
                        class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 bg-white transition-all">
                    @foreach($stages as $stage)
                        <option value="{{ $stage->id }}" {{ $loop->first ? 'selected' : '' }}>
                            {{ $stage->name }} ({{ $stage->probability }}%)
                        </option>
                    @endforeach
                </select>
                @error('pipeline_stage_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Expected Close Date --}}
            <div>
                <label for="deal-close-date" class="block text-xs font-mono text-charcoal-400 uppercase mb-1">Expected Close Date</label>
                <input type="date" id="deal-close-date" name="expected_close_date"
                       value="{{ old('expected_close_date') }}"
                       class="w-full px-4 py-2.5 border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 transition-all">
                @error('expected_close_date')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-charcoal-100">
                <a href="{{ route('sales.leads.show', $lead) }}" class="text-sm text-charcoal-600 hover:text-charcoal-800 transition-colors">← Kembali ke Lead</a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Deal
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
