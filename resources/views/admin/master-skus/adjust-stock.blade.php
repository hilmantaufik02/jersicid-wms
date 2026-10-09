<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    {{ __('Tambah Stok: ') . $sku->sku }}
                </h2>
                <p class="text-sm text-slate-400 mt-1">{{ $sku->product_name }} ({{ $sku->size }})</p>
            </div>
            <a href="{{ route('master-skus.index') }}" class="text-sm text-cyan-400 hover:text-cyan-300 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        @if(session('error'))
            <div class="p-4 bg-red-900/30 border border-red-500/50 text-red-400 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Info Stok Saat Ini -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Lokasi Stok Saat Ini
            </h3>

            @if($existingStocks->isEmpty())
                <div class="text-center py-6 bg-slate-900/50 rounded-xl border border-slate-700/50 border-dashed">
                    <p class="text-sm text-slate-400">Belum ada stok untuk SKU ini di gudang manapun.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($existingStocks as $stock)
                        <div class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg border border-slate-700/50">
                            <div>
                                <p class="text-sm font-mono font-bold text-cyan-400">{{ $stock->bin_code }}</p>
                                <p class="text-xs text-slate-500">Batch: {{ $stock->batch_no ?: '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-white">{{ number_format($stock->qty) }}</p>
                                <p class="text-[10px] text-slate-400">unit</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Form Tambah Stok -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah Stok Baru
            </h3>

            <form method="POST" action="{{ route('master-skus.store-adjust-stock', $sku->id) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Lokasi Bin *</label>
                        <input type="text" name="bin_code" value="{{ old('bin_code') }}" required 
                               placeholder="Contoh: RAK-A-01-01"
                               class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all font-mono"/>
                        <p class="text-xs text-slate-500 mt-1">Format: RAK-[HURUF]-[2 DIGIT]-[2 DIGIT]</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Jumlah Ditambahkan *</label>
                        <input type="number" name="qty_to_add" value="{{ old('qty_to_add') }}" required min="1"
                               placeholder="0"
                               class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">No. Batch</label>
                        <input type="text" name="batch_no" value="{{ old('batch_no') }}" 
                               placeholder="Opsional"
                               class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all"/>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Catatan</label>
                        <textarea name="notes" rows="3" 
                                  placeholder="Keterangan penambahan stok (opsional)"
                                  class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4">
                    <a href="{{ route('master-skus.index') }}" class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-xl transition-all">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-slate-900 font-bold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transform hover:scale-[1.02] transition-all duration-200">
                        Simpan Penambahan Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>