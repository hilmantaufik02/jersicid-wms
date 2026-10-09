<div class="more-drawer md:hidden pointer-events-auto"
    :class="{ 'open': moreDrawerOpen }"
    style="z-index: 50;"
    x-cloak>

    <div class="pt-3 pb-2 pointer-events-auto">
        <div class="drawer-handle"></div>
    </div>

    <div class="px-4 pb-6 overflow-y-auto max-h-[60vh] pointer-events-auto">
        <h3 class="text-sm font-semibold text-primary mb-4">Menu Lainnya</h3>

        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('categories.index') }}" @click="moreDrawerOpen = false" class="flex flex-col items-center p-4 bg-elevated border border-subtle rounded-xl touch-target pointer-events-auto" style="z-index: 60;">
                <svg class="w-6 h-6 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span class="text-xs text-primary font-medium">Kategori</span>
            </a>

            <a href="{{ route('suppliers.index') }}" @click="moreDrawerOpen = false" class="flex flex-col items-center p-4 bg-elevated border border-subtle rounded-xl touch-target pointer-events-auto" style="z-index: 60;">
                <svg class="w-6 h-6 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <span class="text-xs text-primary font-medium">Supplier</span>
            </a>

            <a href="{{ route('stock-opname.index') }}" @click="moreDrawerOpen = false" class="flex flex-col items-center p-4 bg-elevated border border-subtle rounded-xl touch-target pointer-events-auto" style="z-index: 60;">
                <svg class="w-6 h-6 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span class="text-xs text-primary font-medium">Stok Opname</span>
            </a>

            <a href="{{ route('returns.index') }}" @click="moreDrawerOpen = false" class="flex flex-col items-center p-4 bg-elevated border border-subtle rounded-xl touch-target pointer-events-auto" style="z-index: 60;">
                <svg class="w-6 h-6 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="text-xs text-primary font-medium">Retur & QC</span>
            </a>

            <a href="{{ route('qrcodes.index') }}" @click="moreDrawerOpen = false" class="flex flex-col items-center p-4 bg-elevated border border-subtle rounded-xl touch-target pointer-events-auto" style="z-index: 60;">
                <svg class="w-6 h-6 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <span class="text-xs text-primary font-medium">Cetak QR</span>
            </a>

            <a href="{{ route('reports.index') }}" @click="moreDrawerOpen = false" class="flex flex-col items-center p-4 bg-elevated border border-subtle rounded-xl touch-target pointer-events-auto" style="z-index: 60;">
                <svg class="w-6 h-6 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span class="text-xs text-primary font-medium">Laporan</span>
            </a>

            <a href="{{ route('orders.daily-report') }}" @click="moreDrawerOpen = false" class="flex flex-col items-center p-4 bg-elevated border border-subtle rounded-xl touch-target pointer-events-auto" style="z-index: 60;">
                <svg class="w-6 h-6 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-xs text-primary font-medium">Lap. Harian</span>
            </a>

            <a href="{{ route('approvals.index') }}" @click="moreDrawerOpen = false" class="flex flex-col items-center p-4 bg-elevated border border-subtle rounded-xl touch-target pointer-events-auto relative" style="z-index: 60;">
                @php $pendingCount = \App\Http\Controllers\Admin\ApprovalController::getPendingCount(); @endphp
                @if($pendingCount > 0)
                <span class="absolute top-2 right-2 w-5 h-5 rounded-full bg-amber-500 text-white text-[10px] font-bold flex items-center justify-center">{{ $pendingCount }}</span>
                @endif
                <svg class="w-6 h-6 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span class="text-xs text-primary font-medium">Approvals</span>
            </a>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-4 pointer-events-auto">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl font-medium touch-target pointer-events-auto" style="z-index: 60;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </button>
        </form>
    </div>
</div>

<!-- Backdrop untuk More Drawer -->
<div class="fixed inset-0 bg-black/60 z-40 md:hidden transition-opacity pointer-events-none"
    :class="moreDrawerOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
    @click="moreDrawerOpen = false"
    x-cloak></div>