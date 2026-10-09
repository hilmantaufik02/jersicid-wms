<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Picking: {{ $order->order_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: #09090b;
            color: white;
            font-family: 'Inter', sans-serif;
        }

        .glass {
            background: rgba(30, 30, 35, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body class="min-h-screen pb-24" x-data="pickingApp()">
    @php
    // Prepare items data untuk Alpine.js - gunakan function() bukan arrow function
    $itemsData = $order->items->map(function($i) {
    return [
    'id' => $i->id,
    'product_name' => $i->product_name,
    'variant_info' => $i->variant_info,
    'sku' => $i->sku,
    'quantity' => $i->quantity,
    'picked_quantity' => $i->picked_quantity,
    'location_code' => $i->location_code
    ];
    })->values()->toArray();

    // Encode dengan flags yang aman
    $itemsJson = json_encode($itemsData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    @endphp

    <!-- Hidden input untuk pass data ke Alpine.js -->
    <input type="hidden" id="picking-items-data" value='{!! $itemsJson !!}'>
    <input type="hidden" id="order-id" value="{{ $order->id }}">

    <!-- Header -->
    <header class="glass sticky top-0 z-40 p-4 border-b border-zinc-800">
        <div class="flex justify-between items-center mb-2">
            <div>
                <h1 class="font-bold text-lg text-indigo-400">#{{ $order->order_number }}</h1>
                <p class="text-xs text-zinc-500">{{ $order->courier_name ?? 'Belum ada ekspedisi' }} • {{ $order->customer_name }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-zinc-400">Progress</p>
                <p class="font-bold text-emerald-400"><span x-text="pickedCount"></span>/<span x-text="totalCount"></span></p>
            </div>
        </div>
        <div class="w-full bg-zinc-800 rounded-full h-2">
            <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-2 rounded-full transition-all duration-500" :style="`width: ${progress}%`"></div>
        </div>
    </header>

    <!-- Scan Area -->
    <div class="p-4">
        <div class="glass rounded-xl p-4 mb-4">
            <label class="text-xs text-zinc-400 uppercase tracking-wider mb-2 block">Scan SKU Produk</label>
            <div class="flex gap-2">
                <input type="text" x-model="scanInput" @keyup.enter="handleScan" autofocus
                    class="flex-1 bg-zinc-900 border border-zinc-700 rounded-lg px-4 py-3 text-lg text-white focus:outline-none focus:border-indigo-500" placeholder="Scan barcode di sini...">
                <button @click="handleScan" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 rounded-lg font-semibold">OK</button>
            </div>
            <p x-show="scanMessage" class="text-xs mt-2 font-medium" :class="scanSuccess ? 'text-emerald-400' : 'text-red-400'" x-text="scanMessage"></p>
        </div>

        <!-- Items List -->
        <div class="space-y-3">
            <template x-for="item in items" :key="item.id">
                <div class="glass rounded-xl p-4 border-l-4 transition-all" :class="item.picked_quantity >= item.quantity ? 'border-emerald-500 opacity-60' : 'border-indigo-500'">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1 pr-2">
                            <h3 class="font-bold text-white text-sm" x-text="item.product_name"></h3>
                            <p class="text-xs text-zinc-400" x-text="item.variant_info"></p>
                            <p class="text-[10px] text-zinc-600 font-mono mt-1">SKU: <span x-text="item.sku"></span></p>
                            <p class="text-[10px] text-amber-400 font-mono" x-show="item.location_code">Lokasi: <span x-text="item.location_code"></span></p>
                        </div>
                        <div class="text-right">
                            <span class="block text-xl font-bold text-white" x-text="item.quantity"></span>
                            <span class="text-[10px] text-zinc-500">pcs</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-zinc-800">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-zinc-400">Dipick:</span>
                            <span class="font-bold text-emerald-400" x-text="item.picked_quantity"></span>
                        </div>
                        <template x-if="item.picked_quantity < item.quantity">
                            <button @click="quickPick(item)" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 rounded-lg text-xs font-medium text-zinc-300">
                                + Tambah 1
                            </button>
                        </template>
                        <template x-if="item.picked_quantity >= item.quantity">
                            <span class="px-3 py-1.5 bg-emerald-500/20 text-emerald-400 rounded-lg text-xs font-bold flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                SELESAI
                            </span>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Bottom Action Bar -->
    <div class="fixed bottom-0 left-0 right-0 glass border-t border-zinc-800 p-4 z-50">
        <button @click="completePicking" :disabled="!allPicked" :class="allPicked ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-zinc-700 cursor-not-allowed'" class="w-full py-3 rounded-xl font-bold text-white transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Selesai Picking & Kirim ke Packing
        </button>
    </div>

    @push('scripts')
    <script>
        function pickingApp() {
            // Ambil data dari hidden input
            const itemsData = document.getElementById('picking-items-data').value;
            const orderId = document.getElementById('order-id').value;
            const initialItems = itemsData ? JSON.parse(itemsData) : [];

            return {
                items: initialItems,
                orderId: parseInt(orderId),
                scanInput: '',
                scanMessage: '',
                scanSuccess: false,

                get pickedCount() {
                    return this.items.reduce(function(sum, i) {
                        return sum + i.picked_quantity;
                    }, 0);
                },
                get totalCount() {
                    return this.items.reduce(function(sum, i) {
                        return sum + i.quantity;
                    }, 0);
                },
                get progress() {
                    return this.totalCount > 0 ? (this.pickedCount / this.totalCount) * 100 : 0;
                },
                get allPicked() {
                    return this.items.every(function(i) {
                        return i.picked_quantity >= i.quantity;
                    });
                },

                handleScan: function() {
                    const sku = this.scanInput.trim();
                    if (!sku) return;

                    const item = this.items.find(function(i) {
                        return i.sku.toLowerCase() === sku.toLowerCase() && i.picked_quantity < i.quantity;
                    });

                    if (item) {
                        this.updateItem(item.id, item.picked_quantity + 1, sku);
                        this.scanMessage = 'Berhasil: ' + item.product_name;
                        this.scanSuccess = true;
                    } else {
                        this.scanMessage = 'SKU tidak ditemukan atau sudah cukup!';
                        this.scanSuccess = false;
                    }
                    this.scanInput = '';
                    setTimeout(() => {
                        this.scanMessage = '';
                    }, 3000);
                },

                quickPick: function(item) {
                    this.updateItem(item.id, item.picked_quantity + 1, null);
                },

                updateItem: function(itemId, newQty, scannedSku) {
                    const self = this;
                    fetch('/orders/' + this.orderId + '/items/' + itemId + '/picking', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                picked_quantity: newQty,
                                scanned_sku: scannedSku
                            })
                        })
                        .then(function(response) {
                            return response.json();
                        })
                        .then(function(data) {
                            if (data.success) {
                                const index = self.items.findIndex(function(i) {
                                    return i.id === itemId;
                                });
                                if (index !== -1) {
                                    self.items[index].picked_quantity = newQty;
                                }
                            } else {
                                self.scanMessage = data.message;
                                self.scanSuccess = false;
                            }
                        })
                        .catch(function(e) {
                            console.error(e);
                            self.scanMessage = 'Error: Gagal update item';
                            self.scanSuccess = false;
                        });
                },

                completePicking: function() {
                    if (confirm('Yakin semua barang sudah dipick dengan benar?')) {
                        window.location.href = '/orders/' + this.orderId + '/complete-picking';
                    }
                }
            };
        }
    </script>
    @endpush
</body>

</html>