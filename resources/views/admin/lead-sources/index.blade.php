@extends('layouts.partials.app')
@section('title', 'Lead Sources')
@section('page-header', 'Lead Sources')
@section('page-actions')
    <a href="{{ route('admin.lead-sources.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-xl hover:bg-rose-700 focus:ring-4 focus:ring-rose-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Lead Source
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-data="leadSourceManager()">
    @forelse($sources as $source)
        <x-card padding="false" class="flex flex-col relative overflow-hidden group">
            <div class="p-6 flex-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-sm" style="background-color: {{ $source->color ?? '#F43F5E' }}20; color: {{ $source->color ?? '#F43F5E' }}">
                        {{ $source->icon ?? '🎯' }}
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="toggleStatus({{ $source->id }}, $event)" class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2" role="switch" :aria-checked="status[{{ $source->id }}] ? 'true' : 'false'">
                            <span aria-hidden="true" class="pointer-events-none absolute mx-auto h-4 w-8 rounded-full transition-colors duration-200 ease-in-out" :class="status[{{ $source->id }}] ? 'bg-emerald-500' : 'bg-charcoal-200'"></span>
                            <span aria-hidden="true" class="pointer-events-none absolute left-0 inline-block h-5 w-5 transform rounded-full border border-charcoal-200 bg-white shadow ring-0 transition-transform duration-200 ease-in-out" :class="status[{{ $source->id }}] ? 'translate-x-4' : 'translate-x-0'"></span>
                        </button>
                    </div>
                </div>
                
                <h3 class="text-lg font-serif font-bold text-charcoal-900 mb-1">{{ $source->name }}</h3>
                <p class="text-sm text-charcoal-500 mb-4 line-clamp-2">{{ $source->description ?? 'No description provided.' }}</p>
                
                <div class="flex items-center gap-2 text-sm text-charcoal-600 font-medium bg-charcoal-50 rounded-lg p-3">
                    <svg class="w-5 h-5 text-charcoal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    {{ $source->leads_count }} Leads Generated
                </div>
            </div>
            <div class="bg-charcoal-50 px-6 py-3 border-t border-charcoal-100 flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ route('admin.lead-sources.edit', $source) }}" class="text-sm font-medium text-amber-600 hover:text-amber-800">Edit</a>
                <form action="{{ route('admin.lead-sources.destroy', $source) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this lead source?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-800">Delete</button>
                </form>
            </div>
        </x-card>
    @empty
        <div class="col-span-full py-12 text-center text-charcoal-500">
            <div class="w-16 h-16 mx-auto mb-4 bg-charcoal-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-charcoal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <h3 class="text-lg font-medium text-charcoal-900">No lead sources found</h3>
            <p class="mt-1">Get started by creating a new lead source.</p>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('leadSourceManager', () => ({
            status: {
                @foreach($sources as $source)
                {{ $source->id }}: {{ $source->is_active ? 'true' : 'false' }},
                @endforeach
            },
            toggleStatus(sourceId, event) {
                const btn = event.currentTarget;
                btn.disabled = true;
                
                fetch(`/admin/lead-sources/${sourceId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        this.status[sourceId] = data.is_active;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An error occurred.');
                })
                .finally(() => {
                    btn.disabled = false;
                });
            }
        }));
    });
</script>
@endpush
