<x-admin-layout title="Buat Picking List">
    <x-slot name="pageTitle">Buat Picking List Baru</x-slot>

    <form action="{{ route('picking-lists.store') }}" method="POST" x-data="pickingForm()">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Info Picking List -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Informasi Tugas</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Gudang</label>
                            <select name="warehouse_id" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2">
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Prioritas</label>
                            <select name="priority" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2">
                                <option value="low">Low</option>
                                <option value="normal" selected>Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="lg:col-span-2">
                <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-white">Item yang harus diambil</h3>
                        <button type="button" @click="addItem()" class="text-xs bg-cyan-600 hover:bg-cyan-700 text-white px-3 py-1 rounded-lg">+ Tambah Item</button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-gray-900 border border-gray-700 rounded-lg p-4 relative group">
                                <button type="button" @click="removeItem(index)" class="absolute top-2 right-2 text-red-400 hover:text-red-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-xs text-gray-400 mb-1">Pilih Varian Produk</label>
                                        <select :name="`items[${index}][variant_id]`" x-model="item.variant_id" required class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded px-3 py-2">
                                            <option value="">-- Pilih Varian --</option>
                                            @foreach($variants as $v)
                                            <option value="{{ $v->id }}">{{ $v->product->name }} - {{ $v->size }} / {{ $v->color }} (Stok: {{ $v->stock }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">Jumlah Ambil</label>
                                        <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" required min="1" class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded px-3 py-2">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="items.length === 0" class="text-center py-8 text-gray-500 border-2 border-dashed border-gray-700 rounded-lg">
                            Belum ada item. Klik "+ Tambah Item".
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('picking-lists.index') }}" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold">Simpan Picking List</button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function pickingForm() {
            return {
                items: [{
                    variant_id: '',
                    quantity: 1
                }],
                addItem() {
                    this.items.push({
                        variant_id: '',
                        quantity: 1
                    });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>