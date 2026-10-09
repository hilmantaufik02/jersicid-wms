<x-admin-layout title="Packing Order">
    <x-slot name="pageTitle">Packing: #{{ $order->order_number }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Info Order -->
        <div class="glass-panel p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-xl font-bold text-zinc-100">Order #{{ $order->order_number }}</h2>
                    <p class="text-sm text-zinc-400">{{ $order->courier_name }} • {{ $order->customer_name }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-500/10 text-purple-400 border border-purple-500/20">Sedang Packing</span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-zinc-500 text-xs">No. Resi</p>
                    <p class="font-mono text-zinc-200">{{ $order->tracking_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-zinc-500 text-xs">Marketplace</p>
                    <p class="text-zinc-200 capitalize">{{ $order->marketplace }}</p>
                </div>
                <div>
                    <p class="text-zinc-500 text-xs">Total Item</p>
                    <p class="text-zinc-200 font-bold">{{ $order->total_items }} pcs</p>
                </div>
                <div>
                    <p class="text-zinc-500 text-xs">Alamat</p>
                    <p class="text-zinc-200 truncate">{{ $order->city }}, {{ $order->province }}</p>
                </div>
            </div>
        </div>

        <!-- Packing List -->
        <div class="glass-panel p-6">
            <h3 class="text-sm font-semibold text-zinc-100 mb-4">Daftar Barang yang Harus Dikemas</h3>
            <div class="space-y-3">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between p-4 bg-zinc-900/50 border border-zinc-800 rounded-xl">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-zinc-100">{{ $item->product_name }}</p>
                            <p class="text-xs text-zinc-500">{{ $item->variant_info }} • SKU: {{ $item->sku }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-zinc-100">{{ $item->quantity }}x</p>
                        <p class="text-xs text-zinc-500">Sudah di-pick</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Action Form -->
        <form action="{{ route('orders.complete-packing', $order) }}" method="POST" class="glass-panel p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Catatan Packing (Opsional)</label>
                <textarea name="notes" rows="3" class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-3 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500" placeholder="Contoh: Dikemas dengan bubble wrap tebal, fragile...">{{ $order->notes }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('orders.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-purple-600 hover:bg-purple-500 shadow-lg shadow-purple-500/20 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Selesai Packing & Kirim ke QC
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>