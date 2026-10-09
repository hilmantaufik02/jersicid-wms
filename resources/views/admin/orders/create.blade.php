<x-admin-layout title="Input Order Baru">
    <x-slot name="pageTitle">Input Order Baru</x-slot>

    <form action="{{ route('orders.store') }}" method="POST" class="space-y-6" x-data="orderForm()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Info Order & Customer -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Pesanan -->
                <div class="glass-panel p-6">
                    <h3 class="text-sm font-semibold text-zinc-100 mb-4">Informasi Pesanan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1.5">No. Pesanan (Marketplace)</label>
                            <input type="text" name="order_number" x-model="orderNumber" @change="detectCourier" required
                                class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" placeholder="Contoh: 26062...">
                            @error('order_number') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1.5">No. Resi (Scan/Manual)</label>
                            <input type="text" name="tracking_number" x-model="trackingNumber" @keyup.enter="detectCourier"
                                class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" placeholder="Scan barcode resi di sini...">
                            @error('tracking_number') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1.5">Marketplace</label>
                            <select name="marketplace" required class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500">
                                <option value="shopee">Shopee</option>
                                <option value="tiktok">TikTok Shop</option>
                                <option value="tokopedia">Tokopedia</option>
                                <option value="lazada">Lazada</option>
                                <option value="walk-in">Walk-in / Offline</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1.5">Ekspedisi (Auto-detect)</label>
                            <div class="relative">
                                <input type="text" name="courier_name" x-model="courierName" readonly
                                    class="w-full bg-zinc-800/50 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-emerald-400 font-semibold focus:outline-none" placeholder="Akan terdeteksi otomatis...">
                                <div x-show="courierDetected" class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <input type="hidden" name="courier_service" x-model="courierService">
                        </div>
                    </div>
                </div>

                <!-- Info Customer -->
                <div class="glass-panel p-6">
                    <h3 class="text-sm font-semibold text-zinc-100 mb-4">Informasi Customer</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1.5">Nama Penerima</label>
                            <input type="text" name="customer_name" required class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1.5">No. HP</label>
                            <input type="text" name="customer_phone" required class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1.5">Kota</label>
                            <input type="text" name="city" class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                            <textarea name="customer_address" rows="2" required class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Items & Summary -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Add Item -->
                <div class="glass-panel p-6">
                    <h3 class="text-sm font-semibold text-zinc-100 mb-4">Tambah Produk</h3>
                    <div class="relative mb-3">
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchProduct"
                            class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500"
                            placeholder="Scan SKU atau ketik nama produk...">

                        <!-- Search Results Dropdown -->
                        <div x-show="searchResults.length > 0" class="absolute z-50 w-full mt-1 bg-zinc-800 border border-zinc-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                            <template x-for="product in searchResults" :key="product.id">
                                <div @click="addItem(product)" class="px-4 py-3 hover:bg-zinc-700 cursor-pointer border-b border-zinc-700 last:border-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-zinc-100" x-text="product.product_name"></p>
                                            <p class="text-xs text-zinc-400" x-text="product.variant_info + ' | SKU: ' + product.sku"></p>
                                        </div>
                                        <span class="text-xs text-emerald-400" x-text="'Stok: ' + product.stock"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-3 relative group">
                                <button type="button" @click="removeItem(index)" class="absolute top-2 right-2 text-red-400 hover:text-red-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <input type="hidden" :name="`items[${index}][variant_id]`" :value="item.id">
                                <input type="hidden" :name="`items[${index}][unit_price]`" :value="item.price">

                                <p class="text-sm font-medium text-zinc-100 pr-6" x-text="item.product_name"></p>
                                <p class="text-xs text-zinc-500 mb-2" x-text="item.variant_info"></p>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-zinc-400">Qty:</label>
                                        <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" min="1" required
                                            class="w-16 bg-zinc-800 border border-zinc-700 rounded px-2 py-1 text-sm text-zinc-100 text-center">
                                    </div>
                                    <span class="text-sm font-semibold text-emerald-400" x-text="formatRupiah(item.price * item.quantity)"></span>
                                </div>
                            </div>
                        </template>
                        <div x-show="items.length === 0" class="text-center py-6 text-zinc-500 text-xs border-2 border-dashed border-zinc-800 rounded-lg">
                            Belum ada produk. Scan atau cari di atas.
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="glass-panel p-6">
                    <h3 class="text-sm font-semibold text-zinc-100 mb-4">Ringkasan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-zinc-400">
                            <span>Subtotal</span>
                            <span x-text="formatRupiah(subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-zinc-400">
                            <span>Ongkir</span>
                            <input type="number" name="shipping_cost" x-model="shippingCost" min="0" class="w-24 bg-zinc-900 border border-zinc-700 rounded px-2 py-1 text-right text-zinc-100">
                        </div>
                        <div class="flex justify-between text-zinc-400">
                            <span>Diskon</span>
                            <input type="number" name="discount" x-model="discount" min="0" class="w-24 bg-zinc-900 border border-zinc-700 rounded px-2 py-1 text-right text-zinc-100">
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-3 border-t border-zinc-800">
                            <span class="text-zinc-100">Total</span>
                            <span class="text-emerald-400" x-text="formatRupiah(total)"></span>
                        </div>
                    </div>
                    <button type="submit" :disabled="items.length === 0" :class="items.length === 0 ? 'bg-zinc-700 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-500/20'" class="w-full mt-4 py-3 rounded-xl text-white font-semibold transition-all">
                        Simpan Order
                    </button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function orderForm() {
            return {
                orderNumber: '',
                trackingNumber: '',
                courierName: '',
                courierService: '',
                courierDetected: false,
                searchQuery: '',
                searchResults: [],
                items: [],
                shippingCost: 0,
                discount: 0,

                get subtotal() {
                    return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },
                get total() {
                    return this.subtotal + parseFloat(this.shippingCost || 0) - parseFloat(this.discount || 0);
                },

                formatRupiah(amount) {
                    return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
                },

                async detectCourier() {
                    if (this.orderNumber && this.trackingNumber) {
                        try {
                            const response = await fetch('{{ route("orders.detect-courier") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    order_number: this.orderNumber,
                                    tracking_number: this.trackingNumber
                                })
                            });
                            const data = await response.json();
                            if (data.detected) {
                                this.courierName = data.courier_name;
                                this.courierService = data.courier_service;
                                this.courierDetected = true;
                            } else {
                                this.courierName = '';
                                this.courierDetected = false;
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }
                },

                async searchProduct() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    try {
                        const response = await fetch('{{ route("orders.search-product") }}?q=' + this.searchQuery);
                        this.searchResults = await response.json();
                    } catch (e) {
                        console.error(e);
                    }
                },

                addItem(product) {
                    // Cek apakah produk sudah ada
                    const existing = this.items.find(i => i.id === product.id);
                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.items.push({
                            ...product,
                            quantity: 1
                        });
                    }
                    this.searchQuery = '';
                    this.searchResults = [];
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>