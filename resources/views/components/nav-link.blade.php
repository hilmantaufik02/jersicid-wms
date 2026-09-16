@props(['active' => false, 'icon' => '', 'text' => ''])

@php
    $classes = ($active ?? false)
        ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 font-medium transition-all group relative'
        : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-700/50 hover:text-white font-medium transition-all group relative';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <!-- Icon (Selalu Muncul) -->
    <div class="flex-shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {{ $icon }}
        </svg>
    </div>
    
    <!-- Text (Muncul hanya jika sidebar terbuka) -->
    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap text-sm">
        {{ $text }}
    </span>
    
    <!-- Tooltip (Muncul hanya jika sidebar tertutup, saat di-hover) -->
    <div x-show="!sidebarOpen" x-transition class="absolute left-full ml-2 px-2 py-1 bg-slate-900 border border-slate-700 rounded text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none z-50">
        {{ $text }}
    </div>
</a>