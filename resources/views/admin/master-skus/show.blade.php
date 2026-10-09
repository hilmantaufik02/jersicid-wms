<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">{{ $sku->product_name }}</h2>
                <p class="text-sm text-slate-400 mt-1">Artikel: {{ $sku->article }} • {{ $sku->size_category }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Info Produk -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-base font-semibold text-white mb-4">Informasi Produk</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-slate-400 mb-1">Artikel</p>
                    <p class="text-lg font-bold text-white">{{ $sku->article }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Kategori</p>
                    <p class="text-lg font-bold text-purple-400">{{ $sku->size_category }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Harga</p>
                    <p class="text-lg font-bold text-emerald-400">Rp {{ number_format($sku->price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Status</p>
                    <span class="text-sm px-3 py-1 rounded-full {{ $sku->status === 'AKTIF' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-400' }}">
                        {{ $sku->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Semua Varian -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-base font-semibold text-white mb-4">Semua Varian Size</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-400 border-b border-slate-800">
                            <th class="text-left py-2 px-3">SKU</th>
                            <th class="text-left py-2 px-3">Size</th>
                            <th class="text-left py-2 px-3">Version</th>
                            <th class="text-center py-2 px-3">Total Stok</th>
                            <th class="text-center py-2 px-3">Status</th>
                            <th class="text-center py-2 px-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($product as $variant)
                        @php
                            $variantStock = $variant->stockActives->sum('qty');
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors {{ $variant->id == $sku->id ? 'bg-cyan-900/10' : '' }}">
                            <td class="py-3 px-3 font-mono text-cyan-400 font-bold text-xs">{{ $variant->sku }}</td>
                            <td class="py-3 px-3">
                                <span class="text-xs font-bold text-white bg-slate-800 px-2 py-1 rounded">{{ $variant->size }}</span>
                            </td>
                            <td class="py-3 px-3 text-xs text-slate-400">v{{ $variant->version }}{{ $variant->sub_version ? '.' . $variant->sub_version : '' }}</td>
                            <td class="py-3 px-3 text-center">
                                <span class="text-sm font-bold {{ $variantStock > 0 ? 'text-emerald-400' : 'text-slate-500' }}">
                                    {{ number_format($variantStock) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="text-xs px-2 py-1 rounded-full {{ $variant->status === 'AKTIF' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-400' }}">
                                    {{ $variant->status }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('master-skus.edit', $variant->id) }}" class="p-1.5 text-slate-400 hover:text-amber-400 hover:bg-slate-800 rounded transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('master-skus.destroy', $variant->id) }}" class="inline" onsubmit="return confirm('Yakin hapus {{ $variant->sku }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-slate-800 rounded transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stok per Bin (untuk SKU yang dipilih) -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-base font-semibold text-white mb-4">Stok {{ $sku->sku }} per Lokasi Bin</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-400 border-b border-slate-800">
                            <th class="text-left py-2 px-3">Kode Bin</th>
                            <th class="text-right py-2 px-3">Qty</th>
                            <th class="text-left py-2 px-3">Terakhir Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($sku->stockActives as $stock)
                        <tr>
                            <td class="py-2 px-3 font-mono text-cyan-400">{{ $stock->bin_code }}</td>
                            <td class="py-2 px-3 text-right font-bold text-emerald-400">{{ number_format($stock->qty) }}</td>
                            <td class="py-2 px-3 text-xs text-slate-400">{{ $stock->last_movement_at?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-6 text-slate-500">Tidak ada stok untuk SKU ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
            <a href="{{ route('master-skus.edit', $sku->id) }}" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white font-semibold rounded-xl shadow-lg transition-all flex items-center gap-2 border border-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit SKU Ini
            </a>
            <a href="{{ route('master-skus.index') }}" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-xl border border-slate-700 transition-all">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</x-app-layout>