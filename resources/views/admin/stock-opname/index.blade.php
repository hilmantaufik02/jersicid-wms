<x-admin-layout title="Stock Opname">
    <x-slot name="pageTitle">Stock Opname (Penyesuaian Stok Massal)</x-slot>

    @if(session('success'))
    <div class="alert-success px-4 py-3 rounded-lg text-sm mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert-error px-4 py-3 rounded-lg text-sm mb-4">
        @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ route('stock-opname.store') }}" method="POST" class="space-y-6" x-data="stockOpnameForm({{ json_encode($products->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'sku' => $p->sku,
        'variants' => $p->variants->map(fn($v) => [
            'id' => $v->id,
            'sku' => $v->sku,
            'size' => $v->size,
            'color' => $v->color,
            'stock' => $v->stock,
            'min_stock' => $v->min_stock,
        ])
    ])) }})">
        @csrf

        <!-- Header Info -->
        <div class="glass-panel p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Gudang</label>
                    <input type="text" name="warehouse" class="form-input w-full" value="Gudang Utama Cimahi" readonly>
                </div>
                <div>
                    <label class="form-label">Catatan Umum</label>
                    <input type="text" name="general_notes" class="form-input w-full" placeholder="Opname bulanan, dll...">
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="glass-panel p-6">
            <div class="flex flex-col md:flex-row gap-3 mb-4">
                <input type="text" x-model="searchQuery" @input="filterProducts()"
                    placeholder="Cari produk atau SKU..." class="form-input flex-1">
                <select x-model="filterCategory" @change="filterProducts()" class="form-input md:w-48">
                    <option value="">Semua Produk</option>
                    <option value="low">Stok Menipis</option>
                    <option value="zero">Stok Habis</option>
                </select>
                <button type="button" @click="selectAll()" class="btn-ghost px-4 py-2 rounded-lg text-sm">
                    ✓ Pilih Semua
                </button>
                <button type="button" @click="deselectAll()" class="btn-ghost px-4 py-2 rounded-lg text-sm">
                    ✗ Batal Semua
                </button>
            </div>

            <div class="text-xs text-muted mb-3">
                Dipilih: <span class="font-bold text-primary" x-text="selectedCount"></span> varian
            </div>

            <!-- Tabel Massal -->
            <div class="overflow-x-auto">
                <table class="precise-table">
                    <thead>
                        <tr>
                            <th class="w-10">
                                <input type="checkbox" @click="toggleAll()" class="w-4 h-4 rounded">
                            </th>
                            <th>Produk</th>
                            <th>Varian</th>
                            <th>SKU</th>
                            <th class="text-center">Stok Sistem</th>
                            <th class="text-center">Stok Fisik</th>
                            <th class="text-center">Selisih</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="product in filteredProducts" :key="product.id">
                            <template x-for="variant in product.variants" :key="variant.id">
                                <tr :class="isSelected(variant.id) ? 'bg-indigo-500/5' : ''">
                                    <td>
                                        <input type="checkbox"
                                            :value="variant.id"
                                            @click="toggleVariant(variant.id, product.id)"
                                            :checked="isSelected(variant.id)"
                                            class="w-4 h-4 rounded">
                                    </td>
                                    <td>
                                        <div class="text-sm font-medium text-primary" x-text="product.name"></div>
                                        <div class="text-xs text-muted" x-text="product.sku"></div>
                                    </td>
                                    <td>
                                        <span class="text-sm text-primary" x-text="variant.size + ' / ' + variant.color"></span>
                                    </td>
                                    <td class="font-mono text-xs text-muted" x-text="variant.short_sku || variant.sku"></td>
                                    <td class="text-center">
                                        <span class="text-sm font-semibold"
                                            :class="variant.stock <= variant.min_stock ? 'text-amber-400' : 'text-primary'"
                                            x-text="variant.stock"></span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            x-model.number="physicalStocks[variant.id]"
                                            @input="calculateDifference(variant.id, variant.stock)"
                                            min="0"
                                            class="form-input w-20 text-center text-sm py-1"
                                            :disabled="!isSelected(variant.id)">
                                    </td>
                                    <td class="text-center">
                                        <span class="text-sm font-bold"
                                            :class="differences[variant.id] > 0 ? 'text-emerald-400' : (differences[variant.id] < 0 ? 'text-red-400' : 'text-muted')"
                                            x-text="formatDifference(differences[variant.id])"></span>
                                    </td>
                                    <td>
                                        <input type="text"
                                            x-model="variantNotes[variant.id]"
                                            placeholder="Alasan..."
                                            class="form-input w-full text-xs py-1"
                                            :disabled="!isSelected(variant.id)">
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="filteredProducts.length === 0" class="text-center py-10 text-muted">
                Tidak ada produk ditemukan.
            </div>
        </div>

        <!-- Hidden inputs untuk submit -->
        <template x-for="variantId in selectedVariants" :key="variantId">
            <div>
                <input type="hidden" name="items[]" :value="variantId" :name="`items[${variantId}][variant_id]`">
                <input type="hidden" :name="`items[${variantId}][physical_stock]`" :value="physicalStocks[variantId] || 0">
                <input type="hidden" :name="`items[${variantId}][notes]`" :value="variantNotes[variantId] || ''">
            </div>
        </template>

        <!-- Submit Button -->
        <div class="flex justify-between items-center glass-panel p-4">
            <div class="text-sm text-muted">
                Total varian dipilih: <span class="font-bold text-primary" x-text="selectedCount"></span>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('stock-opname.index') }}" class="btn-ghost px-6 py-2 rounded-lg">Reset</a>
                <button type="submit"
                    :disabled="selectedCount === 0"
                    :class="selectedCount === 0 ? 'opacity-50 cursor-not-allowed' : 'bg-emerald-600 hover:bg-emerald-500'"
                    class="text-white px-6 py-2 rounded-lg font-semibold">
                    Simpan & Sesuaikan Stok
                </button>
            </div>
        </div>
    </form>

    <!-- Riwayat -->
    <div class="glass-panel p-6 mt-6">
        <h3 class="text-lg font-semibold text-primary mb-4">Riwayat Stock Opname</h3>

        @if($history->count() > 0)
        <div class="space-y-3">
            @foreach($history as $opname)
            <div class="bg-elevated border border-subtle rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-sm font-semibold text-primary">{{ $opname->warehouse }}</p>
                        <p class="text-xs text-muted">
                            Oleh: {{ $opname->user->name }} • {{ $opname->created_at->format('d M Y H:i') }}
                        </p>
                    </div>
                    <span class="px-2 py-1 rounded-md text-xs font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                        {{ $opname->total_items }} items
                    </span>
                </div>
                @if($opname->general_notes)
                <p class="text-xs text-muted mb-2">{{ $opname->general_notes }}</p>
                @endif
                <div class="text-xs text-muted">
                    Disesuaikan: <span class="font-bold text-emerald-400">{{ $opname->adjusted_count }}</span> varian
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-center text-muted py-4">Belum ada riwayat opname.</p>
        @endif
    </div>

    @push('scripts')
    <script>
        function stockOpnameForm(products) {
            return {
                products: products,
                filteredProducts: products,
                searchQuery: '',
                filterCategory: '',
                selectedVariants: [],
                physicalStocks: {},
                variantNotes: {},
                differences: {},

                get selectedCount() {
                    return this.selectedVariants.length;
                },

                filterProducts() {
                    let filtered = this.products;

                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(p =>
                            p.name.toLowerCase().includes(query) ||
                            p.sku.toLowerCase().includes(query)
                        );
                    }

                    if (this.filterCategory === 'low') {
                        filtered = filtered.filter(p =>
                            p.variants.some(v => v.stock <= v.min_stock && v.stock > 0)
                        );
                    } else if (this.filterCategory === 'zero') {
                        filtered = filtered.filter(p =>
                            p.variants.some(v => v.stock === 0)
                        );
                    }

                    this.filteredProducts = filtered;
                },

                isSelected(variantId) {
                    return this.selectedVariants.includes(variantId);
                },

                toggleVariant(variantId, productId) {
                    const index = this.selectedVariants.indexOf(variantId);
                    if (index > -1) {
                        this.selectedVariants.splice(index, 1);
                        delete this.physicalStocks[variantId];
                        delete this.variantNotes[variantId];
                        delete this.differences[variantId];
                    } else {
                        this.selectedVariants.push(variantId);
                        // Auto-fill dengan stok sistem
                        const product = this.products.find(p => p.id === productId);
                        const variant = product.variants.find(v => v.id === variantId);
                        this.physicalStocks[variantId] = variant.stock;
                        this.differences[variantId] = 0;
                    }
                },

                toggleAll() {
                    const allSelected = this.filteredProducts.every(p =>
                        p.variants.every(v => this.selectedVariants.includes(v.id))
                    );

                    if (allSelected) {
                        this.deselectAll();
                    } else {
                        this.selectAll();
                    }
                },

                selectAll() {
                    this.filteredProducts.forEach(p => {
                        p.variants.forEach(v => {
                            if (!this.selectedVariants.includes(v.id)) {
                                this.selectedVariants.push(v.id);
                                this.physicalStocks[v.id] = v.stock;
                                this.differences[v.id] = 0;
                            }
                        });
                    });
                },

                deselectAll() {
                    this.selectedVariants = [];
                    this.physicalStocks = {};
                    this.variantNotes = {};
                    this.differences = {};
                },

                calculateDifference(variantId, systemStock) {
                    const physical = this.physicalStocks[variantId] || 0;
                    this.differences[variantId] = physical - systemStock;
                },

                formatDifference(diff) {
                    if (diff > 0) return '+' + diff;
                    if (diff < 0) return diff.toString();
                    return '0';
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>