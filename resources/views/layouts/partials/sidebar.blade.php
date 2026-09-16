<aside 
    class="sidebar-transition bg-slate-800 border-r border-slate-700 flex flex-col relative flex-shrink-0 z-20"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
>
    <!-- Logo Section -->
    <div class="h-16 flex items-center justify-center border-b border-slate-700 px-4 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/5 to-transparent animate-pulse-glow"></div>
        
        <div class="relative z-10 flex items-center gap-3 w-full justify-center">
            <div class="w-9 h-9 bg-gradient-to-br from-cyan-400 to-cyan-600 rounded-lg flex items-center justify-center shadow-lg glow-cyan flex-shrink-0">
                <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <h2 x-show="sidebarOpen" x-transition class="text-lg font-bold text-cyan-400 tracking-wider whitespace-nowrap">
                JERSIC<span class="text-white">.WMS</span>
            </h2>
        </div>
    </div>

    <!-- Toggle Button (Di dalam sidebar, di bagian bawah logo) -->
    <div class="px-4 py-2 border-b border-slate-700">
        <button 
            @click="sidebarOpen = !sidebarOpen"
            class="w-full flex items-center justify-center bg-slate-700/50 hover:bg-slate-700 text-cyan-400 rounded-lg py-2 transition-all glow-cyan"
            :title="sidebarOpen ? 'Tutup Sidebar' : 'Buka Sidebar'"
        >
            <svg class="w-5 h-5 transition-transform duration-300" 
                 :class="sidebarOpen ? 'rotate-0' : 'rotate-180'" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
    </div>

    <!-- Workspace Indicator -->
    <div class="px-4 py-3 border-b border-slate-700" x-show="sidebarOpen" x-transition>
        <p class="text-[10px] text-slate-500 uppercase tracking-wider mb-1">Workspace Aktif</p>
        <p class="text-xs font-semibold text-cyan-400 flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
            {{ Auth::user()->workspace_default->value }}
        </p>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        @php
            $workspace = Auth::user()->workspace_default->value;
        @endphp

        @if($workspace === 'UTAMA')
            <x-nav-link href="/dashboard/owner" :active="request()->is('dashboard*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></x-slot>
                <x-slot name="text">Dashboard</x-slot>
            </x-nav-link>
            
            <x-nav-link href="/master-data" :active="request()->is('master-data*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></x-slot>
                <x-slot name="text">Master SKU</x-slot>
            </x-nav-link>
            
            <x-nav-link href="/audit-trail" :active="request()->is('audit-trail*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></x-slot>
                <x-slot name="text">Audit Trail</x-slot>
            </x-nav-link>

        @elseif($workspace === 'GUDANG')
            <x-nav-link href="/gudang/inbound" :active="request()->is('gudang/inbound*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></x-slot>
                <x-slot name="text">Inbound</x-slot>
            </x-nav-link>
            
            <x-nav-link href="/gudang/outbound" :active="request()->is('gudang/outbound*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></x-slot>
                <x-slot name="text">Outbound</x-slot>
            </x-nav-link>
            
            <x-nav-link href="/gudang/opname" :active="request()->is('gudang/opname*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></x-slot>
                <x-slot name="text">Stock Opname</x-slot>
            </x-nav-link>
        @endif
    </nav>

    <!-- User Profile Bottom -->
    <div class="p-3 border-t border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center text-slate-900 font-bold shadow-lg glow-cyan flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div x-show="sidebarOpen" x-transition class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->role->value }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen" x-transition class="flex-shrink-0">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>