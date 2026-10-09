<x-admin-layout title="Edit Produk">
    <x-slot name="pageTitle">Edit Produk: {{ $product->name }}</x-slot>

    <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informasi Dasar -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-panel p-6">
                    <h3 class="text-lg font-semibold text-primary mb-4">Informasi Dasar</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="form-input w-full">
                            @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">SKU Induk</label>
                                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="form-input w-full sku-input" placeholder="Contoh: JER-N00">
                            </div>
                            <div>
                                <label class="form-label">Kategori</label>
                                <select name="category_id" class="form-input w-full">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
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
                                    <option value="{{ $sup->id }}" {{ $product->supplier_id == $sup->id ? 'selected' : '' }}>
                                        {{ $sup->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Brand</label>
                                <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" class="form-input w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Season</label>
                                <input type="text" name="season" value="{{ old('season', $product->season) }}" class="form-input w-full">
                            </div>
                            <div>
                                <label class="form-label">Tahun</label>
                                <input type="number" name="year" value="{{ old('year', $product->year) }}" class="form-input w-full">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="3" class="form-input w-full">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Varian -->
            <div class="lg:col-span-1">
                <div class="glass-panel p-6 sticky top-24">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-primary">Varian</h3>
                        <button type="button" id="addVariantBtn" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium">
                            + Tambah
                        </button>
                    </div>

                    <div id="variantsContainer" class="space-y-3 max-h-[600px] overflow-y-auto">
                        <!-- Variants will be rendered here by JavaScript -->
                    </div>

                    <div class="mt-4 pt-4 border-t border-subtle">
                        <p class="text-xs text-muted">* Harga kosong = otomatis dari marketplace</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden inputs untuk variants -->
        <div id="variantsHiddenInputs"></div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('products.index') }}" class="btn-ghost px-6 py-2 rounded-lg">Batal</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2 rounded-lg font-semibold">
                Update Produk
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        // Data variants dari server
        const initialVariants = @json($variants);
        let variants = JSON.parse(JSON.stringify(initialVariants));
        let variantCounter = variants.length;

        // Render variants
        function renderVariants() {
            const container = document.getElementById('variantsContainer');
            const hiddenInputs = document.getElementById('variantsHiddenInputs');

            container.innerHTML = '';
            hiddenInputs.innerHTML = '';

            variants.forEach((variant, index) => {
                // Render card
                const card = document.createElement('div');
                card.className = 'bg-elevated border border-subtle rounded-lg p-3';
                card.innerHTML = `
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-semibold text-primary">Varian ${index + 1}</span>
                        <button type="button" onclick="removeVariant(${index})" 
                                class="text-red-400 hover:text-red-300 text-xs"
                                ${variants.length > 1 ? '' : 'style="display:none"'}>
                            ✕ Hapus
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="text-[10px] text-muted uppercase">Ukuran</label>
                            <select onchange="updateVariant(${index}, 'size', this.value)" class="form-input w-full text-sm py-1.5">
                                <option value="S" ${variant.size === 'S' ? 'selected' : ''}>S</option>
                                <option value="M" ${variant.size === 'M' ? 'selected' : ''}>M</option>
                                <option value="L" ${variant.size === 'L' ? 'selected' : ''}>L</option>
                                <option value="XL" ${variant.size === 'XL' ? 'selected' : ''}>XL</option>
                                <option value="XXL" ${variant.size === 'XXL' ? 'selected' : ''}>XXL</option>
                                <option value="XXXL" ${variant.size === 'XXXL' ? 'selected' : ''}>XXXL</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-muted uppercase">Warna</label>
                            <input type="text" value="${variant.color || ''}" 
                                   onchange="updateVariant(${index}, 'color', this.value)"
                                   class="form-input w-full text-sm py-1.5" placeholder="Hitam Putih">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="text-[10px] text-muted uppercase">Stok</label>
                            <input type="number" value="${variant.stock || 0}" min="0"
                                   onchange="updateVariant(${index}, 'stock', this.value)"
                                   class="form-input w-full text-sm py-1.5">
                        </div>
                        <div>
                            <label class="text-[10px] text-muted uppercase">Min Stok</label>
                            <input type="number" value="${variant.min_stock || 5}" min="0"
                                   onchange="updateVariant(${index}, 'min_stock', this.value)"
                                   class="form-input w-full text-sm py-1.5">
                        </div>
                        <div>
                            <label class="text-[10px] text-muted uppercase">Harga*</label>
                            <input type="number" value="${variant.price_override || ''}" min="0"
                                   onchange="updateVariant(${index}, 'price_override', this.value)"
                                   class="form-input w-full text-sm py-1.5" placeholder="Auto">
                        </div>
                    </div>
                `;
                container.appendChild(card);

                // Render hidden inputs
                const hiddenDiv = document.createElement('div');
                hiddenDiv.innerHTML = `
                    <input type="hidden" name="variants[${index}][id]" value="${variant.id || ''}">
                    <input type="hidden" name="variants[${index}][size]" value="${variant.size}">
                    <input type="hidden" name="variants[${index}][color]" value="${variant.color}">
                    <input type="hidden" name="variants[${index}][stock]" value="${variant.stock}">
                    <input type="hidden" name="variants[${index}][min_stock]" value="${variant.min_stock}">
                    <input type="hidden" name="variants[${index}][price_override]" value="${variant.price_override || ''}">
                `;
                hiddenInputs.appendChild(hiddenDiv);
            });
        }

        function updateVariant(index, field, value) {
            variants[index][field] = value;
            renderVariants();
        }

        function addVariant() {
            variants.push({
                id: null,
                size: 'M',
                color: '',
                stock: 0,
                min_stock: 5,
                price_override: null,
            });
            renderVariants();
        }

        function removeVariant(index) {
            if (variants.length > 1) {
                variants.splice(index, 1);
                renderVariants();
            }
        }

        // Event listeners
        document.getElementById('addVariantBtn').addEventListener('click', addVariant);

        // Initial render
        renderVariants();

        // Auto-uppercase untuk SKU
        document.querySelectorAll('.sku-input').forEach(input => {
            input.addEventListener('input', function(e) {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
    @endpush
</x-admin-layout>