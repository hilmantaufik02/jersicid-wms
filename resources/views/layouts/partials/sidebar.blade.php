<aside class="w-64 bg-wms-card border-r border-wms-border flex flex-col">
    <!-- Logo -->
    <div class="h-16 flex items-center justify-center border-b border-wms-border px-4">
        <h2 class="text-xl font-bold text-wms-accent tracking-widest">JERSIC<span class="text-white">.WMS</span></h2>
    </div>

    <!-- Workspace Indicator -->
    <div class="p-4 border-b border-wms-border">
        <p class="text-xs text-wms-muted uppercase tracking-wider">Workspace Aktif</p>
        <p class="text-sm font-semibold text-wms-accent mt-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-wms-accent animate-pulse"></span>
            {{ Auth::user()->workspace_default->value }}
        </p>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        @php
            $workspace = Auth::user()->workspace_default->value;
        @endphp

        @if($workspace === 'UTAMA')
            <x-nav-link href="/dashboard" :active="request()->is('dashboard')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></x-slot>
                Dashboard
            </x-nav-link>
            <x-nav-link href="/master-data" :active="request()->is('master-data*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></x-slot>
                Master SKU
            </x-nav-link>
            <x-nav-link href="/audit-trail" :active="request()->is('audit-trail*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></x-slot>
                Audit Trail
            </x-nav-link>

        @elseif($workspace === 'GUDANG')
            <x-nav-link href="/gudang/inbound" :active="request()->is('gudang/inbound*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></x-slot>
                Inbound (Masuk)
            </x-nav-link>
            <x-nav-link href="/gudang/outbound" :active="request()->is('gudang/outbound*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></x-slot>
                Outbound (Keluar)
            </x-nav-link>
            <x-nav-link href="/gudang/stock-opname" :active="request()->is('gudang/stock-opname*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></x-slot>
                Stock Opname
            </x-nav-link>

        @elseif($workspace === 'PACKING')
            <x-nav-link href="/packing/scan" :active="request()->is('packing/scan*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4M4 20h4m12 0h4M4 4h4m12 0h4" /></x-slot>
                Scan Packing
            </x-nav-link>
            <x-nav-link href="/packing/manifest" :active="request()->is('packing/manifest*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></x-slot>
                Manifest Pengiriman
            </x-nav-link>

        @elseif($workspace === 'PO')
            <x-nav-link href="/po/sublim" :active="request()->is('po/sublim*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></x-slot>
                PO Sublimasi
            </x-nav-link>
            <x-nav-link href="/po/konveksi" :active="request()->is('po/konveksi*')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></x-slot>
                PO Konveksi
            </x-nav-link>
        @endif
    </nav>

    <!-- User Profile Bottom -->
    <div class="p-4 border-t border-wms-border">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-wms-accent/20 flex items-center justify-center text-wms-accent font-bold">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-wms-muted truncate">{{ Auth::user()->role->value }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-wms-muted hover:text-red-400 transition-colors" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </div>
</aside>