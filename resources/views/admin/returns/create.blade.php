<x-admin-layout title="Buat Retur">
    <x-slot name="pageTitle">Buat Permintaan Retur</x-slot>

    <form action="{{ route('returns.store') }}" method="POST"
          id="return-form"
          data-trans-items="{{ json_encode($transactions->mapWithKeys(fn($t) => [$t->id => $t->items->map(fn($i) => [
              'id' => $i->id, 'product_name' => $i->product_name, 'variant_name' => $i->variant_name, 'quantity' => $i->quantity, 'unit_price' => $i->unit_price
          ])])) }}"
          data-transactions="{{ json_encode($transactions) }}"
          x-data="returnForm()">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Pilih Transaksi Asal</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">No. Invoice</label>
                            <select name="transaction_id" x-model="selectedTransId" @change="loadTransaction" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2">
                                <option value="">-- Pilih Invoice --</option>
                                @foreach($transactions as $trans)
                                <option value="{{ $trans->id }}">{{ $trans->invoice_number }} - {{ $trans->customer_name }} ({{ format_rupiah($trans->total) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Nama Customer Retur</label>
                            <input type="text" name="customer_name" x-model="customerName" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Alasan Retur</label>
                            <textarea name="reason" rows="3" required class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2" placeholder="Contoh: Salah ukuran, ada cacat sablon..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg border border-gray-700 p-6" x-show="selectedTransId">
                    <h3 class="text-lg font-semibold text-white mb-4">Pilih Item yang Dikembalikan</h3>
                    <div class="space-y-3">
                        <template x-for="(item, index) in transItems" :key="index">
                            <div class="bg-gray-900 border border-gray-600 rounded-lg p-4 flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="font-bold text-white" x-text="item.product_name"></p>
                                    <p class="text-sm text-gray-400" x-text="item.variant_name + ' | Qty Beli: ' + item.quantity"></p>
                                    <p class="text-cyan-400 text-sm font-bold" x-text="formatRupiah(item.unit_price)"></p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <label class="text-xs text-gray-400">Qty Retur:</label>
                                    <input type="number" :name="`items[${index}][transaction_item_id]`" :value="item.id" class="hidden">
                                    <input type="number" :name="`items[${index}][quantity]`" x-model="item.return_qty" min="1" :max="item.quantity" class="w-20 bg-gray-700 border border-gray-600 text-white rounded px-2 py-1 text-center">
                                </div>
                            </div>
                        </template>
                        <div x-show="transItems.length === 0" class="text-center py-8 text-gray-500">
                            Pilih invoice terlebih dahulu.
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Estimasi Refund</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-400">
                            <span>Total Item Retur:</span>
                            <span x-text="totalReturnQty + ' pcs'"></span>
                        </div>
                        <div class="flex justify-between text-xl font-bold pt-3 border-t border-gray-700">
                            <span>Total Refund:</span>
                            <span class="text-cyan-400" x-text="formatRupiah(totalRefund)"></span>
                        </div>
                    </div>
                    <button type="submit" :disabled="totalReturnQty === 0" :class="totalReturnQty === 0 ? 'bg-gray-700 cursor-not-allowed' : 'bg-gradient-to-r from-purple-600 to-cyan-600 hover:from-purple-700 hover:to-cyan-700'" class="w-full mt-6 py-3 rounded-lg font-bold text-white transition">
                        Simpan Permintaan Retur
                    </button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function returnForm() {
            return {
                selectedTransId: '',
                customerName: '',
                transItems: [],
                get totalReturnQty() {
                    return this.transItems.reduce((sum, i) => sum + parseInt(i.return_qty || 0), 0);
                },
                get totalRefund() {
                    return this.transItems.reduce((sum, i) => sum + (parseInt(i.return_qty || 0) * parseFloat(i.unit_price)), 0);
                },

                formatRupiah(amount) {
                    return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
                },

                loadTransaction() {
                    if (!this.selectedTransId) {
                        this.transItems = [];
                        return;
                    }
                    var formEl = document.getElementById('return-form');
                    var transData = JSON.parse(formEl.dataset.transItems || '{}');
                    var allTrans = JSON.parse(formEl.dataset.transactions || '[]');

                    this.transItems = (transData[this.selectedTransId] || []).map(i => ({
                        ...i,
                        return_qty: 0
                    }));

                    // Auto fill customer name
                    var selectedTrans = allTrans.find(t => t.id == this.selectedTransId);
                    if (selectedTrans) this.customerName = selectedTrans.customer_name;
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>