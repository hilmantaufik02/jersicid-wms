<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Stock Opname') }}
            </h2>
            <p class="text-sm text-slate-400 mt-1">Pencocokan stok fisik dengan sistem</p>
        </div>
    </x-slot>

    <div class="space-y-6">

        @if(session('success'))
            <div class="p-4 bg-emerald-900/30 border border-emerald-500/50 text-emerald-400 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-900/30 border border-red-500/50 text-red-400 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Form Opname -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Form Stock Opname
            </h3>

            <form method="POST" action="{{ route('gudang.opname.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">SKU Produk *</label>
                        <select name="sku" id="opname_sku" required class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all">
                            <option value="">Pilih SKU</option>
                            @foreach($skus as $sku)
                                <option value="{{ $sku->sku }}" {{ old('sku') == $sku->sku ? 'selected' : '' }}>
                                    {{ $sku->sku }} - {{ $sku->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Lokasi Bin *</label>
                        <select name="bin_code" id="opname_bin" required class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all">
                            <option value="">Pilih Lokasi Bin</option>
                        </select>
                        <p id="system_stock_info" class="text-xs text-slate-500 mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Stok Fisik (Actual) *</label>
                        <input type="number" name="actual_stock" id="opname_actual" value="{{ old('actual_stock') }}" required min="0"
                               placeholder="0"
                               class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Selisih</label>
                        <div id="difference_display" class="px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white font-bold text-lg">
                            0
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Catatan</label>
                    <textarea name="notes" rows="3" 
                              placeholder="Keterangan opname..."
                              class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-400 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 transform hover:scale-[1.02] transition-all duration-200">
                    Simpan Stock Opname
                </button>
            </form>
        </div>

        <!-- Riwayat Opname -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 rounded-2xl shadow-xl">
            <div class="flex items-center justify-between p-6 border-b border-slate-700/50">
                <h3 class="text-lg font-semibold text-white">Riwayat Stock Opname</h3>
                <span class="text-xs text-slate-400">{{ $opnames->total() }} total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-xs text-slate-400 border-b border-slate-700/50">
                            <th class="text-left py-3 px-6">Tanggal</th>
                            <th class="text-left py-3 px-6">SKU</th>
                            <th class="text-left py-3 px-6">Bin</th>
                            <th class="text-left py-3 px-6">Sistem</th>
                            <th class="text-left py-3 px-6">Fisik</th>
                            <th class="text-left py-3 px-6">Selisih</th>
                            <th class="text-left py-3 px-6">User</th>
                            <th class="text-left py-3 px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($opnames as $opname)
                            <tr class="border-b border-slate-700/30 hover:bg-slate-800/50 transition-colors">
                                <td class="py-4 px-6">
                                    <span class="text-xs text-slate-300">{{ $opname->created_at->format('d M Y H:i') }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div>
                                        <p class="text-sm font-mono text-white font-medium">{{ $opname->sku }}</p>
                                        <p class="text-xs text-slate-500">{{ $opname->masterSku?->product_name ?? '-' }}</p>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-xs px-2 py-1 rounded-full bg-cyan-500/20 text-cyan-400">
                                        {{ $opname->bin_code ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-sm text-slate-300">{{ number_format($opname->system_stock) }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-sm font-bold text-white">{{ number_format($opname->actual_stock) }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $diff = $opname->difference;
                                        $diffClass = $diff > 0 ? 'text-emerald-400' : ($diff < 0 ? 'text-red-400' : 'text-slate-400');
                                        $diffPrefix = $diff > 0 ? '+' : '';
                                    @endphp
                                    <span class="text-sm font-bold {{ $diffClass }}">
                                        {{ $diffPrefix }}{{ number_format($diff) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-xs text-slate-300">{{ $opname->user?->name }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $opname->status === 'approved' ? 'bg-emerald-500/20 text-emerald-400' : '' }}
                                        {{ $opname->status === 'pending' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                        {{ $opname->status === 'rejected' ? 'bg-red-500/20 text-red-400' : '' }}">
                                        {{ strtoupper($opname->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-sm text-slate-500">
                                    Belum ada riwayat opname
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($opnames->hasPages())
                <div class="p-4 border-t border-slate-700/50">
                    {{ $opnames->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
    // Data stok untuk filter bin
    const stockData = @json($stockData);

    document.getElementById('opname_sku').addEventListener('change', function() {
        const sku = this.value;
        const binSelect = document.getElementById('opname_bin');
        const systemStockInfo = document.getElementById('system_stock_info');
        
        binSelect.innerHTML = '<option value="">Pilih Lokasi Bin</option>';
        systemStockInfo.textContent = '';
        
        if (!sku) return;
        
        const stockForSku = stockData.filter(s => s.sku === sku && s.qty > 0);
        
        stockForSku.forEach(stock => {
            const option = document.createElement('option');
            option.value = stock.bin_code;
            option.textContent = `${stock.bin_code} (Sistem: ${stock.qty})`;
            option.dataset.systemQty = stock.qty;
            binSelect.appendChild(option);
        });
    });

    document.getElementById('opname_bin').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const systemStockInfo = document.getElementById('system_stock_info');
        
        if (selectedOption && selectedOption.dataset.systemQty) {
            systemStockInfo.textContent = `Stok sistem: ${selectedOption.dataset.systemQty} unit`;
            systemStockInfo.className = 'text-xs text-cyan-400 mt-1';
        } else {
            systemStockInfo.textContent = '';
        }
    });

    // Hitung selisih real-time
    document.getElementById('opname_actual').addEventListener('input', function() {
        const binSelect = document.getElementById('opname_bin');
        const selectedOption = binSelect.options[binSelect.selectedIndex];
        const systemQty = parseInt(selectedOption.dataset.systemQty || 0);
        const actualQty = parseInt(this.value || 0);
        const difference = actualQty - systemQty;
        
        const display = document.getElementById('difference_display');
        display.textContent = (difference > 0 ? '+' : '') + difference;
        
        if (difference > 0) {
            display.className = 'px-4 py-3 bg-emerald-900/30 border border-emerald-500/50 rounded-xl text-emerald-400 font-bold text-lg';
        } else if (difference < 0) {
            display.className = 'px-4 py-3 bg-red-900/30 border border-red-500/50 rounded-xl text-red-400 font-bold text-lg';
        } else {
            display.className = 'px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-slate-400 font-bold text-lg';
        }
    });
    </script>
</x-app-layout>