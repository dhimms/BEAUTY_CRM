@props(['color' => 'gray', 'size' => 'sm'])

@php
    $colors = [
        'rose' => 'bg-rose-50 text-rose-700 border-rose-200',
        'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
        'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
        'gray' => 'bg-gray-50 text-gray-700 border-gray-200',
        'red' => 'bg-red-50 text-red-700 border-red-200',
    ];
    $sizes = [
        'xs' => 'px-2 py-0.5 text-[10px]',
        'sm' => 'px-2.5 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-3.5 py-2 text-base',
    ];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-semibold rounded-full border tracking-wide uppercase {$colors[$color]} {$sizes[$size]}"]) }}>
    {{ $slot }}
</span>