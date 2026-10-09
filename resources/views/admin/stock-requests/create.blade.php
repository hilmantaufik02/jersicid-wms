<x-admin-layout title="Ajukan Permintaan Stok">
    <x-slot name="pageTitle">Ajukan Permintaan Stok</x-slot>

    <div class="max-w-2xl mx-auto">
        <form action="{{ route('stock-requests.store') }}" method="POST" enctype="multipart/form-data" class="glass-panel p-6">
            @csrf

            <!-- Tipe Permintaan -->
            <div class="mb-6">
                <label class="form-label">Tipe Permintaan</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="request_type" value="restock" checked
                            class="peer sr-only" onchange="toggleRequestType('restock')">
                        <div class="p-4 rounded-xl border-2 border-zinc-800 peer-checked:border-indigo-600 peer-checked:bg-indigo-600/10 text-center transition-all">
                            <div class="font-semibold text-primary">Restock</div>
                            <div class="text-xs text-muted mt-1">Stok menipis</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="request_type" value="new_product"
                            class="peer sr-only" onchange="toggleRequestType('new_product')">
                        <div class="p-4 rounded-xl border-2 border-zinc-800 peer-checked:border-purple-600 peer-checked:bg-purple-600/10 text-center transition-all">
                            <div class="font-semibold text-primary">Produk Baru</div>
                            <div class="text-xs text-muted mt-1">Rilis produk baru</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Form untuk Restock -->
            <div id="restockFields" class="space-y-4">
                <div>
                    <label class="form-label">Pilih Produk</label>
                    <select name="product_id" id="productSelect" class="form-input w-full">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            data-variants="{{ $product->variants->map(function($v) {
                                    return json_encode([
                                        'id' => $v->id,
                                        'size' => $v->size,
                                        'color' => $v->color,
                                        'stock' => $v->stock
                                    ]);
                                })->toJson() }}">
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Pilih Varian</label>
                    <select name="variant_id" id="variantSelect" class="form-input w-full" disabled>
                        <option value="">-- Pilih produk terlebih dahulu --</option>
                    </select>
                </div>
            </div>

            <!-- Form untuk Produk Baru -->
            <div id="newProductFields" class="space-y-4 hidden">
                <div>
                    <label class="form-label">Nama Produk Baru</label>
                    <input type="text" name="product_name" class="form-input w-full" placeholder="Contoh: Jersey Timnas Indonesia 2025">
                </div>

                <div>
                    <label class="form-label">Upload Mockup/Desain</label>
                    <input type="file" name="mockup_file" accept="image/*,.pdf" class="form-input w-full">
                    <p class="text-xs text-muted mt-1">Format: JPG, PNG, PDF (Max: 5MB)</p>
                </div>

                <div>
                    <label class="form-label">Catatan Sample</label>
                    <textarea name="sample_notes" rows="3" class="form-input w-full" placeholder="Spesifikasi sample yang diinginkan..."></textarea>
                </div>
            </div>

            <!-- Jumlah Permintaan -->
            <div class="mt-4">
                <label class="form-label">Jumlah Permintaan</label>
                <input type="number" name="quantity_requested" min="1" required class="form-input w-full" placeholder="Masukkan jumlah yang diminta">
            </div>

            <!-- Catatan Umum -->
            <div>
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="notes" rows="3" class="form-input w-full" placeholder="Informasi tambahan..."></textarea>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('stock-requests.index') }}" class="btn-ghost px-6 py-2 rounded-lg">Batal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2 rounded-lg font-semibold">
                    Ajukan Permintaan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function toggleRequestType(type) {
            const restockFields = document.getElementById('restockFields');
            const newProductFields = document.getElementById('newProductFields');

            if (type === 'restock') {
                restockFields.classList.remove('hidden');
                newProductFields.classList.add('hidden');
            } else {
                restockFields.classList.add('hidden');
                newProductFields.classList.remove('hidden');
            }
        }

        // Variant selector (sama seperti sebelumnya)
        document.addEventListener('DOMContentLoaded', function() {
            const productSelect = document.getElementById('productSelect');
            const variantSelect = document.getElementById('variantSelect');

            if (productSelect) {
                productSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const variantsData = selectedOption.getAttribute('data-variants');

                    variantSelect.innerHTML = '<option value="">-- Pilih Varian --</option>';

                    if (variantsData && variantsData !== '[]') {
                        try {
                            const variants = JSON.parse(variantsData);

                            if (variants.length > 0) {
                                variantSelect.disabled = false;

                                variants.forEach(function(variant) {
                                    const option = document.createElement('option');
                                    option.value = variant.id;
                                    option.textContent = `${variant.size} / ${variant.color} (Stok: ${variant.stock})`;
                                    variantSelect.appendChild(option);
                                });
                            } else {
                                variantSelect.disabled = true;
                            }
                        } catch (e) {
                            console.error('Error parsing variants:', e);
                            variantSelect.disabled = true;
                        }
                    } else {
                        variantSelect.disabled = true;
                    }
                });
            }
        });
    </script>
    @endpush
</x-admin-layout>