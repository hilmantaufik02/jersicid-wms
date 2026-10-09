<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Laporan & Analytics') }}
            </h2>
            <p class="text-sm text-slate-400 mt-1">Ringkasan aktivitas sistem bulan ini</p>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Movement by Type -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-white mb-4">Pergerakan Stok (Bulan Ini)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($movementByType as $movement)
                    <div class="bg-slate-900/50 border border-slate-700/50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 mb-1">{{ $movement->type }}</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($movement->total_qty) }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $movement->total_transactions }} transaksi</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Two Columns: Top SKUs + Low Stock -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Top SKUs -->
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-white mb-4">Top 10 SKU Paling Aktif</h3>
                <div class="space-y-2">
                    @forelse($topSkus as $index => $sku)
                        <div class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-cyan-400 w-6">#{{ $index + 1 }}</span>
                                <span class="text-sm text-white font-mono">{{ $sku->sku }}</span>
                            </div>
                            <span class="text-sm font-semibold text-white">{{ number_format($sku->total_movement) }} unit</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>

            <!-- Low Stock -->
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-white mb-4">Stok Menipis</h3>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @forelse($lowStockItems as $item)
                        <div class="flex items-center justify-between p-3 bg-red-900/20 border border-red-500/30 rounded-lg">
                            <div>
                                <p class="text-sm text-white font-mono">{{ $item->sku }}</p>
                                <p class="text-xs text-slate-400">{{ $item->product_name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-red-400">{{ $item->qty }} unit</p>
                                <p class="text-xs text-slate-500">Min: {{ $item->min_stock }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-emerald-400 text-center py-4">✓ Semua stok aman</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- User Activity -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-white mb-4">Aktivitas User (Bulan Ini)</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-xs text-slate-400 border-b border-slate-700/50">
                            <th class="text-left py-2 px-3">#</th>
                            <th class="text-left py-2 px-3">Nama</th>
                            <th class="text-left py-2 px-3">Role</th>
                            <th class="text-left py-2 px-3">Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userActivity as $index => $user)
                            <tr class="border-b border-slate-700/30 hover:bg-slate-800/50 transition-colors">
                                <td class="py-3 px-3 text-xs font-bold text-cyan-400">#{{ $index + 1 }}</td>
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $user->profile_photo_url }}" class="w-8 h-8 rounded-full object-cover">
                                        <div>
                                            <p class="text-sm font-medium text-white">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="text-xs px-2 py-1 rounded-full bg-cyan-500/20 text-cyan-400">{{ $user->role }}</span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="text-sm font-semibold text-white">{{ $user->activity_count }}</span>
                                    <span class="text-xs text-slate-400">aktivitas</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-sm text-slate-500">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>