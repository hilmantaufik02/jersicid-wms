@props(['active' => false, 'icon' => ''])

@php
    $classes = ($active ?? false)
                ? 'flex items-center gap-3 px-4 py-2.5 rounded-lg bg-wms-accent/10 text-wms-accent border border-wms-accent/20 font-medium transition-all shadow-neon'
                : 'flex items-center gap-3 px-4 py-2.5 rounded-lg text-wms-muted hover:bg-wms-border/50 hover:text-white font-medium transition-all';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {{ $icon }}
    </svg>
    <span>{{ $slot }}</span>
</a>