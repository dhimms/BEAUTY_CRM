@extends('layouts.partials.app')

@section('title', 'Pipeline Overview')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Pipeline</li>
@endsection

@section('page-header', 'Pipeline Overview')
@section('page-subtitle', 'Tampilan deal berdasarkan stage pipeline (read-only)')

@section('content')
<div x-data="pipelineView()" x-init="loadData()">
    {{-- View Toggle --}}
    <div class="flex items-center gap-2 mb-6">
        <button @click="view = 'kanban'" :class="view === 'kanban' ? 'bg-amber-600 text-white' : 'bg-white text-charcoal-600 border border-charcoal-200'"
            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors">Kanban</button>
        <button @click="view = 'list'" :class="view === 'list' ? 'bg-amber-600 text-white' : 'bg-white text-charcoal-600 border border-charcoal-200'"
            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors">List</button>
    </div>

    {{-- Loading --}}
    <div x-show="loading" class="text-center py-12">
        <div class="animate-spin w-8 h-8 border-2 border-amber-600 border-t-transparent rounded-full mx-auto"></div>
        <p class="text-charcoal-500 text-sm mt-3">Memuat data pipeline...</p>
    </div>

    {{-- Kanban View --}}
    <div x-show="!loading && view === 'kanban'" x-cloak class="flex gap-4 overflow-x-auto pb-4" style="min-height: 400px;">
        <template x-for="stage in pipeline" :key="stage.id">
            <div class="flex-shrink-0 w-72 bg-charcoal-50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" :style="`background-color: ${stage.color}`"></div>
                        <h3 class="font-semibold text-charcoal-900 text-sm" x-text="stage.name"></h3>
                    </div>
                    <span class="text-xs font-mono bg-white px-2 py-1 rounded-full text-charcoal-500 border" x-text="stage.count + ' deals'"></span>
                </div>
                <p class="text-xs text-charcoal-400 mb-3">
                    Total: <span class="font-semibold text-charcoal-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(stage.total_value)"></span>
                    <span class="ml-1">(prob: <span x-text="stage.probability"></span>%)</span>
                </p>
                <div class="space-y-3">
                    <template x-for="deal in stage.deals" :key="deal.id">
                        <div class="bg-white rounded-xl p-4 border border-charcoal-200 shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-sm font-semibold text-charcoal-900 mb-1" x-text="deal.name"></p>
                            <p class="text-xs text-charcoal-500 mb-2" x-text="deal.lead_name"></p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-mono font-semibold text-amber-700" x-text="deal.formatted_value"></span>
                                <span class="text-[10px] text-charcoal-400" x-text="deal.expected_close ?? '-'"></span>
                            </div>
                            <p class="text-[10px] text-charcoal-400 mt-1" x-text="'→ ' + deal.assigned_to"></p>
                        </div>
                    </template>
                    <template x-if="stage.deals.length === 0">
                        <p class="text-center text-charcoal-300 text-xs py-6">Tidak ada deal</p>
                    </template>
                </div>
            </div>
        </template>
    </div>

    {{-- List View --}}
    <div x-show="!loading && view === 'list'" x-cloak>
        <x-card :padding="false">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-charcoal-50/50">
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Deal</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Lead</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Stage</th>
                            <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Value</th>
                            <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Weighted</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Sales</th>
                            <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Close Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-charcoal-100">
                        <template x-for="stage in pipeline" :key="stage.id">
                            <template x-for="deal in stage.deals" :key="deal.id">
                                <tr class="hover:bg-charcoal-50/30 transition-colors">
                                    <td class="px-6 py-4 font-medium text-charcoal-900" x-text="deal.name"></td>
                                    <td class="px-6 py-4 text-charcoal-600" x-text="deal.lead_name"></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium">
                                            <span class="w-2 h-2 rounded-full" :style="`background-color: ${stage.color}`"></span>
                                            <span x-text="stage.name"></span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-charcoal-900" x-text="deal.formatted_value"></td>
                                    <td class="px-6 py-4 text-right font-mono text-amber-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(deal.weighted_value)"></td>
                                    <td class="px-6 py-4 text-charcoal-600 text-xs" x-text="deal.assigned_to"></td>
                                    <td class="px-6 py-4 text-charcoal-500 text-xs" x-text="deal.expected_close ?? '-'"></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script>
function pipelineView() {
    return {
        view: 'kanban',
        loading: true,
        pipeline: [],
        async loadData() {
            try {
                const res = await fetch('{{ route("manager.pipeline.data") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.pipeline = data.pipeline || [];
            } catch (e) {
                console.error('Failed to load pipeline data', e);
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endpush
