<x-admin-layout title="Dashboard">
    <x-slot name="pageTitle">Overview</x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-panel p-6">
            <p class="text-xs font-semibold text-muted uppercase">Total Stok</p>
            <p class="text-3xl font-bold text-primary mt-2">{{ number_format($totalStock ?? 0) }}</p>
            <p class="text-xs text-muted mt-1">Unit tersedia</p>
        </div>

        <div class="glass-panel p-6">
            <p class="text-xs font-semibold text-muted uppercase">Penjualan Hari Ini</p>
            <p class="text-3xl font-bold text-primary mt-2">Rp {{ number_format($todaySales ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-muted mt-1">Realtime</p>
        </div>

        <div class="glass-panel p-6">
            <p class="text-xs font-semibold text-muted uppercase">Stok Menipis</p>
            <p class="text-3xl font-bold text-primary mt-2">{{ $lowStockCount ?? 0 }}</p>
            <p class="text-xs text-muted mt-1">Perlu restock</p>
        </div>

        <div class="glass-panel p-6">
            <p class="text-xs font-semibold text-muted uppercase">Pending Orders</p>
            <p class="text-3xl font-bold text-primary mt-2">{{ $pendingOrders ?? 0 }}</p>
            <p class="text-xs text-muted mt-1">Antrian picking</p>
        </div>
    </div>

    <!-- Charts & Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="glass-panel p-6 lg:col-span-2">
            <h3 class="text-sm font-semibold text-primary mb-4">Tren Pendapatan (30 Hari)</h3>
            
            {{-- ✅ GUNAKAN $salesChart SESUAI CONTROLLER --}}
            @if(isset($salesChart) && $salesChart->count() > 0)
                <div class="h-64 flex items-end gap-1">
                    @php 
                        $maxSales = $salesChart->max('total') ?: 1; 
                    @endphp
                    
                    @foreach($salesChart as $day)
                        <div class="flex-1 bg-indigo-500/20 hover:bg-indigo-500/40 transition-all rounded-t-sm relative group" 
                             style="height: {{ max(($day->total / $maxSales) * 100, 2) }}%">
                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-zinc-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none z-10">
                                Rp {{ number_format($day->total, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-64 flex items-center justify-center text-muted text-sm">
                    Belum ada data penjualan
                </div>
            @endif
        </div>

        <div class="glass-panel p-6">
            <h3 class="text-sm font-semibold text-primary mb-4">Transaksi Terbaru</h3>
            
            @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                <div class="space-y-3">
                    @foreach($recentTransactions as $transaction)
                        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-elevated transition-colors">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400 font-bold text-sm">
                                {{ substr($transaction->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-primary truncate">
                                    {{ $transaction->user->name ?? 'Unknown' }}
                                </p>
                                <p class="text-[10px] text-muted">
                                    {{ $transaction->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <p class="text-xs font-bold text-emerald-400">
                                Rp {{ number_format($transaction->total ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-muted text-sm">Belum ada transaksi.</div>
            @endif
        </div>
    </div>
</x-admin-layout>