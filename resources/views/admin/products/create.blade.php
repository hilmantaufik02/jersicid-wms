<x-admin-layout title="Tambah Produk">
    <x-slot name="pageTitle">Tambah Produk Baru</x-slot>

    <form action="{{ route('products.store') }}" method="POST" class="space-y-6" x-data="productForm({{ json_encode($defaultVariants) }})">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informasi Dasar -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-panel p-6">
                    <h3 class="text-lg font-semibold text-primary mb-4">Informasi Dasar</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="name" x-model="product.name" required class="form-input w-full">
                            @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">SKU Induk</label>
                                <<input type="text" name="sku" x-model="product.sku" required class="form-input w-full sku-input" placeholder="Contoh: JER-N00">
                            </div>
                            <div>
                                <label class="form-label">Kategori</label>
                                <select name="category_id" class="form-input w-full">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Supplier</label>
                                <select name="supplier_id" class="form-input w-full">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Brand</label>
                                <input type="text" name="brand" x-model="product.brand" class="form-input w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Season</label>
                                <input type="text" name="season" class="form-input w-full">
                            </div>
                            <div>
                                <label class="form-label">Tahun</label>
                                <input type="number" name="year" value="2025" class="form-input w-full">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="3" class="form-input w-full"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Varian -->
            <div class="lg:col-span-1">
                <div class="glass-panel p-6 sticky top-24">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-primary">Varian</h3>
                        <button type="button" @click="addVariant()" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium">
                            + Tambah
                        </button>
                    </div>

                    <div class="space-y-3 max-h-[600px] overflow-y-auto">
                        <template x-for="(variant, index) in variants" :key="index">
                            <div class="bg-elevated border border-subtle rounded-lg p-3">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-semibold text-primary" x-text="'Varian ' + (index + 1)"></span>
                                    <button type="button" @click="removeVariant(index)"
                                        class="text-red-400 hover:text-red-300 text-xs"
                                        x-show="variants.length > 1">
                                        ✕ Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-2 mb-2">
                                    <div>
                                        <label class="text-[10px] text-muted uppercase">Ukuran</label>
                                        <select x-model="variant.size" class="form-input w-full text-sm py-1.5">
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                            <option value="XXL">XXL</option>
                                            <option value="XXXL">XXXL</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-muted uppercase">Warna</label>
                                        <input type="text" x-model="variant.color" class="form-input w-full text-sm py-1.5" placeholder="Hitam Putih">
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="text-[10px] text-muted uppercase">Stok</label>
                                        <input type="number" x-model.number="variant.stock" min="0" class="form-input w-full text-sm py-1.5">
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-muted uppercase">Min Stok</label>
                                        <input type="number" x-model.number="variant.min_stock" min="0" class="form-input w-full text-sm py-1.5">
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-muted uppercase">Harga*</label>
                                        <input type="number" x-model.number="variant.price_override" min="0" class="form-input w-full text-sm py-1.5" placeholder="Auto">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 pt-4 border-t border-subtle">
                        <p class="text-xs text-muted">* Harga kosong = otomatis dari marketplace</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden inputs untuk variants -->
        <template x-for="(variant, index) in variants" :key="index">
            <div>
                <input type="hidden" :name="`variants[${index}][size]`" :value="variant.size">
                <input type="hidden" :name="`variants[${index}][color]`" :value="variant.color">
                <input type="hidden" :name="`variants[${index}][stock]`" :value="variant.stock">
                <input type="hidden" :name="`variants[${index}][min_stock]`" :value="variant.min_stock">
                <input type="hidden" :name="`variants[${index}][price_override]`" :value="variant.price_override || ''">
            </div>
        </template>

        <div class="flex justify-end gap-3">
            <a href="{{ route('products.index') }}" class="btn-ghost px-6 py-2 rounded-lg">Batal</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2 rounded-lg font-semibold">
                Simpan Produk
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        function productForm(defaultVariants) {
            return {
                product: {
                    name: '',
                    sku: '',
                    brand: '',
                },
                variants: defaultVariants,

                addVariant() {
                    this.variants.push({
                        size: 'M',
                        color: '',
                        stock: 0,
                        min_stock: 5,
                        price_override: null,
                    });
                },

                removeVariant(index) {
                    if (this.variants.length > 1) {
                        this.variants.splice(index, 1);
                    }
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>