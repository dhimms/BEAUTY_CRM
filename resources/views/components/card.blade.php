@props(['padding' => true])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-charcoal-200 shadow-sm']) }}>
    @if($padding)
        <div class="p-6">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>