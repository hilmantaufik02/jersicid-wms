<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir (POS) - jersic.id</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .pos-product-card {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .pos-product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .pos-product-card:active {
            transform: scale(0.98);
        }

        .cart-container {
            position: sticky;
            top: 1rem;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        .cart-items {
            max-height: 400px;
            overflow-y: auto;
        }

        .cart-items::-webkit-scrollbar {
            width: 6px;
        }

        .cart-items::-webkit-scrollbar-track {
            background: transparent;
        }

        .cart-items::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb, #3f3f46);
            border-radius: 10px;
        }

        .category-pill {
            transition: all 0.2s ease;
        }

        .category-pill.active {
            background-color: #4f46e5;
            color: white;
        }

        .cart-item {
            animation: slideIn 0.2s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="bg-primary text-secondary min-h-screen antialiased" x-data="posApp()" x-init="init()" data-products="{{ json_encode($productsData) }}">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="glass-panel border-b border-default px-6 py-4 shrink-0">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-primary">Kasir (POS)</h1>
                    <p class="text-sm text-muted">{{ auth()->user()->name }} • {{ now()->format('d M Y H:i') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="toggleTheme()" class="p-2 rounded-lg btn-ghost">
                        <svg id="sun-icon" class="w-5 h-5 text-amber-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg id="moon-icon" class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn-ghost px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="flex-1 flex overflow-hidden">
            <!-- Products Area -->
            <div class="flex-1 p-6 overflow-y-auto">
                <!-- Search Bar -->
                <div class="glass-panel p-4 mb-4">
                    <div class="relative">
                        <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text"
                            x-model="searchQuery"
                            @input.debounce.300ms="searchProducts()"
                            @keyup.enter="handleBarcodeScan()"
                            placeholder="Scan barcode atau cari produk..."
                            class="form-input w-full pl-10"
                            autofocus>
                    </div>

                    <!-- Category Pills -->
                    <div class="flex gap-2 overflow-x-auto pb-2 mt-4">
                        <button @click="filterByCategory('')"
                            :class="selectedCategory === '' ? 'active' : ''"
                            class="category-pill px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap btn-ghost">
                            Semua
                        </button>
                        @foreach($categories as $cat)
                        <button @click="filterByCategory('{{ $cat->id }}')"
                            :class="selectedCategory === '{{ $cat->id }}' ? 'active' : ''"
                            class="category-pill px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap btn-ghost">
                            {{ $cat->name }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div class="pos-product-card glass-panel p-4 rounded-xl" @click="addToCart(product)">
                            <div class="relative mb-3">
                                <div class="w-full h-32 bg-elevated rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <span class="absolute top-2 right-2 px-2 py-1 rounded-md text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    <span x-text="product.stock"></span> stok
                                </span>
                            </div>
                            <h3 class="font-semibold text-primary text-sm mb-1 truncate" x-text="product.name"></h3>
                            <p class="text-emerald-400 font-bold">Rp <span x-text="formatNumber(product.price)"></span></p>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredProducts.length === 0" class="text-center py-10 text-muted">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-lg font-semibold">Produk tidak ditemukan</p>
                    <p class="text-sm mt-2">Coba kata kunci lain</p>
                </div>
            </div>

            <!-- Floating Cart Sidebar -->
            <div class="w-96 shrink-0 p-4">
                <div class="cart-container glass-panel rounded-xl border border-default flex flex-col" style="height: calc(100vh - 2rem);">
                    <!-- Cart Header -->
                    <div class="p-4 border-b border-default shrink-0">
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-lg font-bold text-primary flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Keranjang
                            </h2>
                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-400" x-text="cart.length + ' item'"></span>
                        </div>
                    </div>

                    <!-- Cart Items -->
                    <div class="flex-1 overflow-y-auto p-4">
                        <template x-if="cart.length === 0">
                            <div class="text-center py-10 text-muted">
                                <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p>Keranjang kosong</p>
                                <p class="text-xs mt-1">Klik produk untuk menambahkan</p>
                            </div>
                        </template>

                        <div class="space-y-3" x-show="cart.length > 0">
                            <template x-for="(item, index) in cart" :key="index">
                                <div class="cart-item bg-elevated border border-subtle rounded-lg p-3">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-medium text-primary text-sm truncate" x-text="item.name"></h4>
                                            <p class="text-xs text-muted">Rp <span x-text="formatNumber(item.price)"></span></p>
                                        </div>
                                        <button @click="removeFromCart(index)"
                                            class="text-muted hover:text-red-400 ml-2 p-1 rounded hover:bg-red-500/10 transition"
                                            title="Hapus produk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click="updateQuantity(index, -1)"
                                            class="w-7 h-7 rounded bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center text-white text-sm transition">
                                            -
                                        </button>
                                        <input type="number"
                                            x-model.number="item.qty"
                                            @change="validateQuantity(index)"
                                            min="1"
                                            class="w-12 text-center bg-transparent border border-subtle rounded text-sm text-primary py-1">
                                        <button @click="updateQuantity(index, 1)"
                                            class="w-7 h-7 rounded bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center text-white text-sm transition">
                                            +
                                        </button>
                                        <span class="ml-auto font-semibold text-primary text-sm">
                                            Rp <span x-text="formatNumber(item.price * item.qty)"></span>
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Cart Footer -->
                    <div class="p-4 border-t border-default shrink-0 bg-card">
                        <div class="space-y-2 text-sm mb-3">
                            <div class="flex justify-between text-muted">
                                <span>Subtotal</span>
                                <span>Rp <span x-text="formatNumber(subtotal)"></span></span>
                            </div>
                            <div class="flex justify-between items-center text-muted">
                                <span>Diskon</span>
                                <input type="number"
                                    x-model.number="discount"
                                    min="0"
                                    class="w-24 text-right bg-transparent border border-subtle rounded px-2 py-1 text-primary">
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-2 border-t border-default">
                                <span class="text-primary">Total</span>
                                <span class="text-emerald-400">Rp <span x-text="formatNumber(total)"></span></span>
                            </div>
                        </div>

                        <button @click="checkout()"
                            :disabled="cart.length === 0"
                            :class="cart.length === 0 ? 'opacity-50 cursor-not-allowed' : 'bg-emerald-600 hover:bg-emerald-500'"
                            class="w-full py-3 rounded-xl font-bold text-white transition-all">
                            Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ LANGSUNG SCRIPT TAG, TANPA @push --}}
    <script>
        function posApp() {
            const products = JSON.parse(document.body.dataset.products || '[]');

            return {
                cart: [],
                searchQuery: '',
                selectedCategory: '',
                discount: 0,
                allProducts: products,
                filteredProducts: [],
                currentTime: '', // ✅ TAMBAHKAN INI
                transactionTime: '', // ✅ TAMBAHKAN INI (untuk checkout)

                init() {
                    this.filteredProducts = this.allProducts;
                    this.startClock(); // ✅ MULAI JAM REALTIME
                    console.log('POS App initialized with', this.allProducts.length, 'products');
                },

                // ✅ FUNGSI JAM REALTIME
                startClock() {
                    const updateClock = () => {
                        const now = new Date();
                        const options = {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: false
                        };
                        this.currentTime = now.toLocaleString('id-ID', options).replace(',', '');
                    };

                    // Update pertama kali
                    updateClock();

                    // Update setiap 1 detik
                    setInterval(updateClock, 1000);
                },

                // ✅ FUNGSI UNTUK FORMAT TIMESTAMP MENJADI FORMAT INVOICE
                formatTransactionTime(timestamp) {
                    if (!timestamp) return '';

                    const date = new Date(timestamp);
                    const options = {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    };

                    // Format: 07 Jul 2026 18:22:50
                    return date.toLocaleString('id-ID', options).replace(',', '');
                },

                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },

                get total() {
                    return this.subtotal - (this.discount || 0);
                },

                formatNumber(num) {
                    return Math.round(num).toLocaleString('id-ID');
                },

                addToCart(product) {
                    const existing = this.cart.find(item => item.id === product.id);
                    if (existing) {
                        existing.qty++;
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            qty: 1
                        });
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                updateQuantity(index, change) {
                    if (change < 0 && this.cart[index].qty <= 1) {
                        this.removeFromCart(index);
                    } else if (change !== 0) {
                        this.cart[index].qty += change;
                    }
                },

                validateQuantity(index) {
                    if (this.cart[index].qty < 1) {
                        this.cart[index].qty = 1;
                    }
                },

                searchProducts() {
                    const query = this.searchQuery.toLowerCase().trim();

                    if (!query) {
                        this.filteredProducts = this.allProducts;
                        return;
                    }

                    const keywords = query.split(' ').filter(k => k.length > 0);

                    this.filteredProducts = this.allProducts.filter(product => {
                        const matchBasic = product.name.toLowerCase().includes(query) ||
                            product.sku.toLowerCase().includes(query);

                        if (matchBasic) return true;

                        return keywords.every(keyword =>
                            product.name.toLowerCase().includes(keyword) ||
                            product.sku.toLowerCase().includes(keyword)
                        );
                    });
                },

                handleBarcodeScan() {
                    const query = this.searchQuery.trim();
                    if (!query) return;

                    const product = this.allProducts.find(p =>
                        p.sku.toLowerCase() === query.toLowerCase()
                    );

                    if (product) {
                        this.addToCart(product);
                        this.searchQuery = '';
                        this.filteredProducts = this.allProducts;
                    } else {
                        this.searchProducts();
                    }
                },

                filterByCategory(categoryId) {
                    this.selectedCategory = categoryId;

                    if (!categoryId) {
                        this.filteredProducts = this.allProducts;
                    } else {
                        this.filteredProducts = this.allProducts.filter(p =>
                            p.category_id == categoryId
                        );
                    }
                },

                checkout() {
                    if (this.cart.length === 0) return;

                    // ✅ Simpan waktu saat tombol diklik
                    this.transactionTime = this.getCurrentTimestamp();

                    const totalFormatted = this.formatNumber(this.total);
                    const timeFormatted = this.currentTime;

                    if (confirm(`Proses transaksi?\n\nTotal: Rp ${totalFormatted}\nWaktu: ${timeFormatted}`)) {
                        fetch('{{ route("pos.transactions.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    items: this.cart,
                                    discount: this.discount,
                                    total: this.total,
                                    transaction_time: this.transactionTime // ✅ KIRIM WAKTU REALTIME
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert(`✅ Transaksi berhasil!\n\nID: ${data.transaction_id}\nWaktu: ${timeFormatted}`);
                                    this.cart = [];
                                    this.discount = 0;
                                    this.transactionTime = '';
                                } else {
                                    alert('❌ Error: ' + data.message);
                                }
                            })
                            .catch(err => {
                                alert('❌ Terjadi kesalahan: ' + err.message);
                            });
                    }
                }
            }
        }
    </script>
</body>

</html>