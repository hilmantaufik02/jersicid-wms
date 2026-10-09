<x-admin-layout title="Laporan Inventaris">
    <x-slot name="pageTitle">Laporan Stok & Valuasi Gudang</x-slot>

    <!-- Valuasi Aset -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-purple-900/50 to-gray-800 rounded-xl border border-purple-500/30 p-6">
            <p class="text-purple-300 text-sm font-semibold">Total Valuasi Stok (HPP)</p>
            <p class="text-4xl font-bold text-white mt-2">{{ format_rupiah($stockValuation->total_valuation ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-2">Nilai aset barang di gudang</p>
        </div>
        <div class="bg-gradient-to-br from-cyan-900/50 to-gray-800 rounded-xl border border-cyan-500/30 p-6">
            <p class="text-cyan-300 text-sm font-semibold">Total Unit Fisik</p>
            <p class="text-4xl font-bold text-white mt-2">{{ number_format($stockValuation->total_units ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-2">Pcs jersey tersedia</p>
        </div>
        <div class="bg-gradient-to-br from-red-900/50 to-gray-800 rounded-xl border border-red-500/30 p-6">
            <p class="text-red-300 text-sm font-semibold">⚠️ Stok Menipis</p>
            <p class="text-4xl font-bold text-white mt-2">{{ $lowStockVariants->count() }}</p>
            <p class="text-xs text-gray-400 mt-2">Varian perlu restock segera</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tabel Stok Menipis -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-700 bg-orange-500/10">
                <h3 class="text-lg font-bold text-orange-400 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Peringatan Stok Menipis
                </h3>
            </div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-900 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-400">Produk</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-400">Varian</th>
                            <th class="px-4 py-3 text-right text-xs text-gray-400">Stok</th>
                            <th class="px-4 py-3 text-right text-xs text-gray-400">Min</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($lowStockVariants as $v)
                        <tr>
                            <td class="px-4 py-3 text-sm text-white">{{ Str::limit($v->product->name, 20) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-400">{{ $v->size }}/{{ $v->color }}</td>
                            <td class="px-4 py-3 text-sm text-red-400 font-bold text-right">{{ $v->stock }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 text-right">{{ $v->min_stock }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">🎉 Semua stok aman!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dead Stock (Barang Tidak Terjual) -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-700 bg-gray-700/50">
                <h3 class="text-lg font-bold text-gray-300 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    Dead Stock (>90 Hari Tidak Terjual)
                </h3>
                <p class="text-xs text-gray-400 mt-1">Pertimbangkan untuk diskon/clearance</p>
            </div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-900 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-400">Produk</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-400">Varian</th>
                            <th class="px-4 py-3 text-right text-xs text-gray-400">Stok Mati</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($deadStock as $v)
                        <tr>
                            <td class="px-4 py-3 text-sm text-white">{{ Str::limit($v->product->name, 20) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-400">{{ $v->size }}/{{ $v->color }}</td>
                            <td class="px-4 py-3 text-sm text-gray-300 font-bold text-right">{{ $v->stock }} pcs</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400">Tidak ada dead stock.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>