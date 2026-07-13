@extends('layouts.partials.app')
@section('title', 'Team Performance')
@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" class="hover:text-amber-600">Dashboard</a></li>
    <li class="text-charcoal-300">/</li>
    <li class="text-charcoal-700 font-medium">Team Performance</li>
@endsection
@section('page-header', 'Team Leaderboard')
@section('page-subtitle', 'Perbandingan performa tim sales')

@section('content')
<x-card :padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-charcoal-50/50">
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-charcoal-500 uppercase">Sales Person</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Leads</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Deals Won</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Revenue</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Win Rate</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-charcoal-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse($leaderboard as $index => $member)
                    <tr class="hover:bg-charcoal-50/30 transition-colors {{ $index === 0 ? 'bg-amber-50/30' : '' }}">
                        <td class="px-6 py-4">
                            @if($index === 0)
                                <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                                    <span class="text-amber-700 font-bold">🥇</span>
                                </div>
                            @elseif($index === 1)
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                    <span class="text-gray-600 font-bold">🥈</span>
                                </div>
                            @elseif($index === 2)
                                <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center">
                                    <span class="text-amber-800 font-bold">🥉</span>
                                </div>
                            @else
                                <span class="text-charcoal-400 font-mono ml-2">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $member['avatar_url'] }}" alt="{{ $member['name'] }}" class="w-10 h-10 rounded-full border-2 {{ $index === 0 ? 'border-amber-400' : 'border-charcoal-200' }}">
                                <div>
                                    <p class="font-semibold text-charcoal-900">{{ $member['name'] }}</p>
                                    <p class="text-xs text-charcoal-400">Sales</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-charcoal-600">{{ $member['leads'] }}</td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-semibold text-emerald-600">{{ $member['won'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-semibold text-charcoal-900">
                            Rp {{ number_format($member['revenue'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <div class="w-16 h-2 bg-charcoal-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $member['win_rate'] >= 50 ? 'bg-emerald-500' : 'bg-amber-500' }}"
                                        style="width: {{ min($member['win_rate'], 100) }}%"></div>
                                </div>
                                <span class="text-sm font-medium {{ $member['win_rate'] >= 50 ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $member['win_rate'] }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('manager.team.show', $member['id']) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-medium hover:bg-amber-100 transition-colors">
                                Detail
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-charcoal-400">Belum ada data sales team.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection
