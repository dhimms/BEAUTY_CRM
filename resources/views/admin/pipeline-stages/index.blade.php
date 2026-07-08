@extends('layouts.partials.app')
@section('title', 'Pipeline Stages')
@section('page-header', 'Pipeline Stages')
@section('page-actions')
    <a href="{{ route('admin.pipeline-stages.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700 focus:ring-4 focus:ring-rose-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Stage
    </a>
@endsection

@section('content')
<div class="mb-4">
    <p class="text-charcoal-500 text-sm">Drag and drop the stages below to reorder your pipeline flow.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="sortable-stages">
    @forelse($stages as $stage)
        <div data-id="{{ $stage->id }}" class="bg-white rounded-xl border border-charcoal-200 shadow-sm overflow-hidden flex flex-col group cursor-move">
            <div class="h-2 w-full" style="background-color: {{ $stage->color ?? '#F43F5E' }}"></div>
            <div class="p-5 flex-1">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-serif font-bold text-lg text-charcoal-900">{{ $stage->name }}</h3>
                    <span class="text-xs font-mono font-medium px-2 py-1 rounded bg-charcoal-50 text-charcoal-600">{{ $stage->probability }}% Win</span>
                </div>
                <div class="flex items-center gap-2 mt-4 text-sm text-charcoal-500 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                    {{ $stage->deals_count }} Deals
                </div>
            </div>
            <div class="bg-charcoal-50 px-5 py-3 border-t border-charcoal-100 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity">
                <svg class="w-5 h-5 text-charcoal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <div class="flex gap-3">
                    <a href="{{ route('admin.pipeline-stages.edit', $stage) }}" class="text-sm font-medium text-amber-600 hover:text-amber-800">Edit</a>
                    <form action="{{ route('admin.pipeline-stages.destroy', $stage) }}" method="POST" class="inline" onsubmit="return confirm('Delete this pipeline stage?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-800">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full py-12 text-center text-charcoal-500">
            <h3 class="text-lg font-medium text-charcoal-900">No pipeline stages found</h3>
            <p class="mt-1">Add your first stage to start tracking deals.</p>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('sortable-stages');
        if(el) {
            new Sortable(el, {
                animation: 150,
                ghostClass: 'opacity-50',
                onEnd: function (evt) {
                    let order = [];
                    el.querySelectorAll('[data-id]').forEach(function (item) {
                        order.push(item.getAttribute('data-id'));
                    });
                    
                    fetch('{{ route("admin.pipeline-stages.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(!data.success) alert('Failed to save order.');
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Error saving order.');
                    });
                },
            });
        }
    });
</script>
@endpush
