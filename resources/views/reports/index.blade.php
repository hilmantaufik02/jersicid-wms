<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-white leading-tight">{{ __('Pusat Laporan') }}</h2>
            <p class="text-sm text-slate-400 mt-1">Rekapitulasi data gudang dan ekspor laporan</p>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Card 1: Laporan Stok -->
        <a href="{{ route('reports.stock') }}" class="bg-gradient-to-br mb-2 from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-6 shadow-xl hover:border-cyan-500/50 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-cyan-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <svg class="w-5 h-5 text-slate-500 group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-1">Laporan Stok Saat Ini</h3>
            <p class="text-sm text-slate-400">Snapshot stok aktif per lokasi bin. Termasuk status low stock.</p>
        </a>

        <!-- Card 2: Pergerakan Stok -->
        <a href="{{ route('reports.movement') }}" class="bg-gradient-to-br mb-2 from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-6 shadow-xl hover:border-emerald-500/50 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <svg class="w-5 h-5 text-slate-500 group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-1">Pergerakan Stok</h3>
            <p class="text-sm text-slate-400">Histori Inbound, Outbound, Transfer, dan Adjustment.</p>
        </a>

        <!-- Card 3: Stock Opname -->
        <a href="{{ route('reports.opname') }}" class="bg-gradient-to-br mb-2 from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-6 shadow-xl hover:border-purple-500/50 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <svg class="w-5 h-5 text-slate-500 group-hover:text-purple-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-1">Laporan Stock Opname</h3>
            <p class="text-sm text-slate-400">Rekap hasil pencocokan stok fisik vs sistem.</p>
        </a>

        <!-- Card 4: Import Master Data (BARU!) -->
        <a href="{{ route('reports.import-sku-form') }}" class="bg-gradient-to-br mb-2 from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-6 shadow-xl hover:border-amber-500/50 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <svg class="w-5 h-5 text-slate-500 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-1">Import Master Data</h3>
            <p class="text-sm text-slate-400">Upload SKU massal via Excel/CSV.</p>
        </a>

    </div>

    <!-- Quick Stats Summary -->
    <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-1">Total SKU Aktif</p>
            <p class="text-2xl font-bold text-cyan-400">{{ \App\Models\MasterSku::where('status', 'AKTIF')->count() }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-1">Total Stok</p>
            <p class="text-2xl font-bold text-emerald-400">{{ number_format(\App\Models\StockActive::sum('qty')) }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-1">Pergerakan Hari Ini</p>
            <p class="text-2xl font-bold text-purple-400">{{ \App\Models\StockMovement::whereDate('created_at', today())->count() }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-1">Opname Selesai</p>
            <p class="text-2xl font-bold text-amber-400">{{ \App\Models\StockOpname::where('status', 'COMPLETED')->count() }}</p>
        </div>
    </div>
</x-app-layout>