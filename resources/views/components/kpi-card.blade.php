@props(['label', 'value', 'icon' => '', 'color' => 'rose', 'trend' => null, 'trendUp' => true])

@php
    $bgColors = [
        'rose' => 'bg-rose-50',
        'blue' => 'bg-blue-50',
        'emerald' => 'bg-emerald-50',
        'amber' => 'bg-amber-50',
        'purple' => 'bg-purple-50',
    ];
    $iconColors = [
        'rose' => 'text-rose-500',
        'blue' => 'text-blue-500',
        'emerald' => 'text-emerald-500',
        'amber' => 'text-amber-500',
        'purple' => 'text-purple-500',
    ];
@endphp

<div class="bg-white rounded-xl border border-charcoal-200 shadow-sm p-6 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs font-mono text-charcoal-400 uppercase tracking-wider">{{ $label }}</p>
            <p class="text-2xl font-serif font-semibold text-charcoal-900 mt-1">{{ $value }}</p>
            @if($trend)
                <div class="flex items-center gap-1 mt-2">
                    @if($trendUp)
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-xs text-emerald-600 font-medium">{{ $trend }}</span>
                    @else
                        <svg class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-xs text-rose-600 font-medium">{{ $trend }}</span>
                    @endif
                    <span class="text-xs text-charcoal-400">vs last month</span>
                </div>
            @endif
        </div>
        <div class="w-12 h-12 rounded-xl {{ $bgColors[$color] }} flex items-center justify-center">
            <svg class="w-6 h-6 {{ $iconColors[$color] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        </div>
    </div>
</div>