@extends('layouts.partials.app')
@section('title', 'Customers Directory')
@section('page-header', 'Customers Directory')

@section('content')
<x-card class="mb-6">
    <form action="{{ route('admin.customers.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone, or tags..." class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm">
        </div>
        
        <div class="w-full md:w-56">
            <select name="status" class="w-full px-4 py-2 bg-charcoal-50 border border-charcoal-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 sm:text-sm" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="churn" {{ request('status') === 'churn' ? 'selected' : '' }}>Churned</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 bg-charcoal-100 text-charcoal-700 hover:bg-charcoal-200 rounded-xl text-sm font-medium transition-colors">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.customers.index') }}" class="ml-2 px-4 py-2 text-rose-600 hover:text-rose-700 text-sm font-medium">Reset</a>
            @endif
        </div>
    </form>
</x-card>

<x-card padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-charcoal-500 uppercase bg-charcoal-50 border-b border-charcoal-200">
                <tr>
                    <th class="px-6 py-4 font-medium">Customer Info</th>
                    <th class="px-6 py-4 font-medium">Tags</th>
                    <th class="px-6 py-4 font-medium">Lifetime Value</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-charcoal-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="font-medium text-charcoal-900 hover:text-rose-600 mb-1 block">{{ $customer->name }}</a>
                            <div class="text-xs text-charcoal-500 flex items-center gap-3">
                                <span>{{ $customer->phone }}</span>
                                @if($customer->email)
                                    <span>•</span>
                                    <span>{{ $customer->email }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if(is_array($customer->tags) && count($customer->tags) > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($customer->tags, 0, 3) as $tag)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-charcoal-100 text-charcoal-700">{{ $tag }}</span>
                                    @endforeach
                                    @if(count($customer->tags) > 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-charcoal-50 text-charcoal-500">+{{ count($customer->tags) - 3 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-charcoal-400 italic text-xs">No tags</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-charcoal-900 font-medium">
                            Rp {{ number_format($customer->total_spent, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :color="$customer->status_color">{{ ucfirst($customer->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="text-amber-600 hover:text-amber-800" title="Edit"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></a>
                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Delete this customer? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800" title="Delete"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-charcoal-500">
                            No customers found. Customers are created automatically when deals are won.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
        <div class="px-6 py-4 border-t border-charcoal-200">
            {{ $customers->links() }}
        </div>
    @endif
</x-card>
@endsection
