<x-admin-layout title="Inspeksi QC">
    <x-slot name="pageTitle">Inspeksi QC: {{ $return->return_number }}</x-slot>

    <div class="bg-yellow-900/30 border border-yellow-600 text-yellow-200 px-4 py-3 rounded-lg mb-6">
        <strong>Alasan Retur:</strong> {{ $return->reason }} <br>
        <strong>Customer:</strong> {{ $return->customer_name }}
    </div>

    <form action="{{ route('returns.process-inspection', $return->id) }}" method="POST">
        @csrf
        <div class="space-y-4">
            @foreach($return->items as $item)
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex justify-between items-start mb-4 border-b border-gray-700 pb-3">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $item->variant->product->name }}</h3>
                        <p class="text-sm text-gray-400">{{ $item->variant->size }} / {{ $item->variant->color }} | Qty: {{ $item->quantity }}</p>
                    </div>
                    <span class="text-cyan-400 font-bold">{{ format_rupiah($item->variant->final_price) }}</span>
                </div>

                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Kondisi Fisik -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Kondisi Fisik Jersey</label>
                        <select name="items[{{ $loop->index }}][condition]" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2">
                            <option value="new">Baru (Tag masih ada, belum dipakai)</option>
                            <option value="used_good">Bekas Layak Jual (Cuci ulang)</option>
                            <option value="used_damaged">Bekas Rusak (Cacat fisik)</option>
                            <option value="defective">Cacat Produksi (Sablon/Jahitan)</option>
                        </select>
                    </div>

                    <!-- Hasil QC -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Hasil Inspeksi QC</label>
                        <select name="items[{{ $loop->index }}][qc_status]" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2">
                            <option value="passed">✅ PASSED (Bisa masuk stok)</option>
                            <option value="failed">❌ FAILED (Reject/Clearance)</option>
                        </select>
                    </div>

                    <!-- Keputusan Restock -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Keputusan Gudang</label>
                        <div class="flex items-center mt-2">
                            <input type="checkbox" name="items[{{ $loop->index }}][restock]" value="1" class="w-5 h-5 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500">
                            <label class="ml-2 text-sm text-gray-300">Masukkan kembali ke stok utama</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Catatan QC (Wajib jika Failed)</label>
                    <textarea name="items[{{ $loop->index }}][notes]" rows="2" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2" placeholder="Contoh: Sablon dada sedikit pecah, perlu cuci ulang..."></textarea>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-between items-center bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center">
                <input type="checkbox" name="process_refund" value="1" class="w-5 h-5 text-green-600 bg-gray-700 border-gray-600 rounded focus:ring-green-500">
                <label class="ml-2 text-white font-semibold">Proses Refund / Tukar Barang ke Customer Sekarang</label>
            </div>
            <button type="submit" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-lg font-bold transition">
                Selesaikan Inspeksi & Update Stok
            </button>
        </div>
    </form>
</x-admin-layout>