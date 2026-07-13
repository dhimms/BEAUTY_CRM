@extends('layouts.partials.app')
@section('title', 'Team Activity Report')
@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li><a href="{{ route('manager.reports.index') }}" class="hover:text-amber-600">Reports</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Team Activity</li>
@endsection
@section('page-header', 'Team Activity Report')
@section('content')
<x-card>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left font-mono font-medium text-charcoal-500 uppercase">Sales Person</th>
                    <th class="px-6 py-3 text-left font-mono font-medium text-charcoal-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left font-mono font-medium text-charcoal-500 uppercase">Subject</th>
                    <th class="px-6 py-3 text-left font-mono font-medium text-charcoal-500 uppercase">Target</th>
                    <th class="px-6 py-3 text-left font-mono font-medium text-charcoal-500 uppercase">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($activities as $activity)
                    <tr class="hover:bg-charcoal-50/30">
                        <td class="px-6 py-4 font-medium text-charcoal-900">{{ $activity->user?->name ?? '-' }}</td>
                        <td class="px-6 py-4"><x-badge color="blue" size="xs">{{ ucfirst($activity->type) }}</x-badge></td>
                        <td class="px-6 py-4 text-charcoal-600">{{ $activity->subject ?? '-' }}</td>
                        <td class="px-6 py-4 text-charcoal-600">{{ $activity->activitable?->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-charcoal-500 text-xs">{{ $activity->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-charcoal-400">Belum ada aktivitas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $activities->links() }}
    </div>
</x-card>
@endsection
