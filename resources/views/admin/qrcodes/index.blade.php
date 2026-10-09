<x-admin-layout title="Cetak QR Code">
    <x-slot name="pageTitle">Cetak QR Code Produk</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary tracking-tight">Cetak QR Code</h1>
            <p class="text-muted text-sm mt-1">Pilih varian dan cetak QR Code (Isi QR: SKU Only)</p>
        </div>
        <form action="{{ route('qrcodes.generate-all') }}" method="POST">
            @csrf
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                Generate Ulang Semua QR
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="mb-6 alert-success px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <!-- FORM PRINT MULTIPLE -->
    <form action="{{ route('qrcodes.print') }}" method="GET" target="_blank" id="printForm">

        <!-- Tombol Print Terpilih (Sticky) -->
        <div class="sticky top-20 z-20 mb-6 glass-panel p-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleAllCheckboxes()" class="text-xs text-indigo-400 hover:text-indigo-300 font-medium">
                    Pilih/Batal Semua
                </button>
                <span class="text-xs text-muted" id="selectedCount">0 varian dipilih</span>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Terpilih
            </button>
        </div>

        <div class="space-y-6">
            @foreach($products as $product)
            <div class="glass-panel p-6">
                <div class="flex items-center gap-4 mb-4 border-b border-zinc-800 pb-4">
                    @if($product->image && Storage::disk('public')->exists($product->image))
                    <img src="{{ asset('storage/' . $product->image) }}" class="w-12 h-12 rounded-lg object-cover">
                    @else
                    <div class="w-12 h-12 rounded-lg bg-elevated flex items-center justify-center text-muted">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    @endif
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-primary">{{ $product->name }}</h3>
                        <p class="text-xs text-muted">{{ $product->category->name ?? '-' }} • {{ $product->variants->count() }} Varian</p>
                    </div>
                    <!-- Tombol Generate per produk -->
                    <form action="{{ route('qrcodes.generate', $product) }}" method="POST" class="ml-auto">
                        @csrf
                        <button type="submit" class="text-xs bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-3 py-1.5 rounded-lg transition-colors">
                            Generate QR Produk Ini
                        </button>
                    </form>
                </div>

                <!-- Grid Varian dengan Checkbox -->
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($product->variants as $variant)
                    <label class="relative flex items-start gap-3 p-3 rounded-lg border border-zinc-800 bg-zinc-900/30 hover:bg-zinc-900/60 cursor-pointer transition-all group has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-500/10">
                        <input type="checkbox" name="variant_ids[]" value="{{ $variant->id }}"
                            class="variant-checkbox mt-1 w-4 h-4 rounded border-zinc-700 bg-zinc-800 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                @if($variant->qr_code && Storage::disk('public')->exists($variant->qr_code))
                                <img src="{{ asset('storage/' . $variant->qr_code) }}" class="w-8 h-8 object-contain">
                                @else
                                <div class="w-8 h-8 bg-zinc-800 rounded flex items-center justify-center text-[8px] text-muted">No QR</div>
                                @endif
                                <span class="text-[10px] font-mono text-muted truncate">{{ $variant->sku }}</span>
                            </div>
                            <p class="text-xs font-medium text-primary truncate">{{ $variant->size }} / {{ $variant->color }}</p>
                            <p class="text-[10px] text-muted">Stok: {{ $variant->stock }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </form>

    @push('scripts')
    <script>
        function toggleAllCheckboxes() {
            const checkboxes = document.querySelectorAll('.variant-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            updateCount();
        }

        // Update counter saat checkbox diklik
        document.querySelectorAll('.variant-checkbox').forEach(cb => {
            cb.addEventListener('change', updateCount);
        });

        function updateCount() {
            const count = document.querySelectorAll('.variant-checkbox:checked').length;
            document.getElementById('selectedCount').innerText = count + ' varian dipilih';
        }
    </script>
    @endpush
</x-admin-layout>