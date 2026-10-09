<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-white leading-tight">{{ __('Stock Transfer - Perpindahan Stok') }}</h2>
            <p class="text-sm text-slate-400 mt-1">Pindahkan stok antar lokasi bin</p>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Flash Messages --}}
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

        {{-- Form Transfer --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Form Perpindahan Stok
            </h3>

            <form method="POST" action="{{ route('gudang.transfer.store') }}"
                  x-data="transferForm()"
                  x-init="init(@js($skus), @js($activeStocks))"
                  class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- SKU --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">SKU Produk *</label>
                        <select name="sku" x-model="selectedSku" @change="onSkuChange()" required
                                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none transition-all">
                            <option value="">Pilih SKU</option>
                            <template x-for="s in skus" :key="s.sku">
                                <option :value="s.sku" x-text="s.sku + ' - ' + s.product_name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Bin Asal --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Bin Asal (Source) *</label>
                        <select name="bin_from" x-model="selectedBinFrom" @change="onBinFromChange()" required
                                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none transition-all">
                            <option value="">Pilih Bin Asal</option>
                            <template x-for="bin in availableBins" :key="bin.bin_code">
                                <option :value="bin.bin_code" x-text="bin.bin_code + ' (Stok: ' + bin.qty + ')'"></option>
                            </template>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Hanya menampilkan bin yang memiliki stok > 0</p>
                    </div>

                    {{-- Bin Tujuan --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Bin Tujuan (Destination) *</label>
                        <input type="text" name="bin_to" x-model="binTo" required placeholder="Contoh: RAK-A-02-01"
                               class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:outline-none transition-all"/>
                        <p class="text-xs text-slate-500 mt-1">Ketik lokasi bin tujuan secara manual</p>
                    </div>

                    {{-- Qty --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Jumlah Dipindahkan (Qty) *</label>
                        <input type="number" name="qty" x-model.number="qty" required min="1" :max="maxQty" placeholder="0"
                               class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:outline-none transition-all"/>
                        <p class="text-xs text-slate-500 mt-1" x-show="maxQty > 0">Maks: <span x-text="maxQty"></span> unit</p>
                    </div>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Catatan / Alasan Transfer</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Merapikan stok dari rak penerimaan ke rak utama..."
                              class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:outline-none transition-all"></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 text-slate-900 font-bold rounded-xl shadow-lg shadow-cyan-500/25 transition-all">
                    Proses Transfer Stok
                </button>
            </form>
        </div>

        {{-- Riwayat Transfer --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-base font-semibold text-white">Riwayat Perpindahan Stok</h3>
                <span class="text-xs text-slate-400 bg-slate-800 px-2 py-1 rounded">{{ $transfers->total() }} total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-400 border-b border-slate-800 bg-slate-800/30">
                            <th class="text-left py-3 px-4">No. Referensi</th>
                            <th class="text-left py-3 px-4">SKU</th>
                            <th class="text-left py-3 px-4">Dari Bin</th>
                            <th class="text-left py-3 px-4">Ke Bin</th>
                            <th class="text-right py-3 px-4">Qty</th>
                            <th class="text-left py-3 px-4">Oleh</th>
                            <th class="text-left py-3 px-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($transfers as $transfer)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-4 font-mono text-xs text-cyan-400 font-bold">{{ $transfer->reference_no }}</td>
                            <td class="py-3 px-4 font-mono text-white">{{ $transfer->sku }}</td>
                            <td class="py-3 px-4 font-mono text-amber-400">{{ $transfer->bin_code }}</td>
                            <td class="py-3 px-4 font-mono text-emerald-400">{{ Str::after($transfer->notes, 'ke ') }}</td>
                            <td class="py-3 px-4 text-right font-bold text-cyan-400">{{ number_format($transfer->qty) }}</td>
                            <td class="py-3 px-4 text-xs text-slate-300">{{ $transfer->user?->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-xs text-slate-400">{{ $transfer->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-500">Belum ada riwayat transfer</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transfers->hasPages())
                <div class="p-4 border-t border-slate-800 bg-slate-800/20">{{ $transfers->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Alpine.js Component --}}
    <script>
    function transferForm() {
        return {
            skus: [],
            activeStocks: {},
            selectedSku: '',
            selectedBinFrom: '',
            binTo: '',
            qty: 0,
            maxQty: 0,
            availableBins: [],

            init(skus, activeStocks) {
                this.skus = skus;
                this.activeStocks = activeStocks;
            },

            onSkuChange() {
                this.selectedBinFrom = '';
                this.binTo = '';
                this.qty = 0;
                this.maxQty = 0;
                this.availableBins = this.activeStocks[this.selectedSku] || [];
            },

            onBinFromChange() {
                const bin = this.availableBins.find(b => b.bin_code === this.selectedBinFrom);
                this.maxQty = bin ? bin.qty : 0;
                this.qty = 0;
            }
        };
    }
    </script>
</x-app-layout>