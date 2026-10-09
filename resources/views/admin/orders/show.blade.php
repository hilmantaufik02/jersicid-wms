<x-admin-layout title="Detail & QC Order">
    <x-slot name="pageTitle">Detail Order #{{ $order->order_number }}</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header Status -->
        <div class="glass-panel p-6 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-zinc-100">Order #{{ $order->order_number }}</h2>
                <p class="text-sm text-zinc-400 mt-1">{{ $order->customer_name }} • {{ $order->courier_name }} ({{ $order->tracking_number }})</p>
            </div>
            @php [$badgeClass, $badgeLabel] = $order->status_badge; @endphp
            <span class="px-4 py-2 rounded-lg text-sm font-semibold border {{ $badgeClass }}">{{ $badgeLabel }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Info & Items -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Info -->
                <div class="glass-panel p-6">
                    <h3 class="text-sm font-semibold text-zinc-100 mb-4">Informasi Pengiriman</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-zinc-500 text-xs">Alamat</p>
                            <p class="text-zinc-200">{{ $order->customer_address }}</p>
                            <p class="text-zinc-200">{{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
                        </div>
                        <div>
                            <p class="text-zinc-500 text-xs">Kontak</p>
                            <p class="text-zinc-200">{{ $order->customer_phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="glass-panel p-6">
                    <h3 class="text-sm font-semibold text-zinc-100 mb-4">Barang dalam Order</h3>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                        <div class="flex items-center justify-between p-3 bg-zinc-900/50 border border-zinc-800 rounded-lg">
                            <div>
                                <p class="font-medium text-zinc-100">{{ $item->product_name }}</p>
                                <p class="text-xs text-zinc-500">{{ $item->variant_info }} • SKU: {{ $item->sku }}</p>
                            </div>
                            <p class="font-bold text-zinc-100">{{ $item->quantity }}x</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: QC Action (Hanya muncul jika status qc_review) -->
            <div class="lg:col-span-1">
                @if($order->status === 'qc_review')
                <div class="glass-panel p-6 border-t-4 border-t-purple-500 sticky top-24">
                    <h3 class="text-sm font-semibold text-zinc-100 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Verifikasi QC (Admin Gudang)
                    </h3>

                    <p class="text-xs text-zinc-400 mb-4">Periksa apakah barang yang dikemas sudah sesuai dengan daftar di atas.</p>

                    <form action="{{ route('orders.qc-verify', $order) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Catatan QC</label>
                            <textarea name="notes" rows="3" class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-purple-500" placeholder="Catatan jika ada masalah..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="submit" name="action" value="reject" class="py-3 rounded-xl text-sm font-semibold text-red-400 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 transition-all">
                                ❌ Tolak
                            </button>
                            <button type="submit" name="action" value="approve" class="py-3 rounded-xl text-sm font-semibold text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all">
                                ✅ Lolos QC
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <!-- Timeline / Info Status -->
                <div class="glass-panel p-6">
                    <h3 class="text-sm font-semibold text-zinc-100 mb-4">Timeline Order</h3>
                    <div class="space-y-4 text-sm">
                        @if($order->picked_at)
                        <div class="flex gap-3">
                            <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5"></div>
                            <div>
                                <p class="text-zinc-200">Picking Selesai</p>
                                <p class="text-xs text-zinc-500">{{ $order->picked_at->format('d M Y, H:i') }} oleh {{ $order->pickedBy->name ?? '-' }}</p>
                            </div>
                        </div>
                        @endif
                        @if($order->packed_at)
                        <div class="flex gap-3">
                            <div class="w-2 h-2 rounded-full bg-purple-400 mt-1.5"></div>
                            <div>
                                <p class="text-zinc-200">Packing Selesai</p>
                                <p class="text-xs text-zinc-500">{{ $order->packed_at->format('d M Y, H:i') }} oleh {{ $order->packedBy->name ?? '-' }}</p>
                            </div>
                        </div>
                        @endif
                        @if($order->qc_verified_at)
                        <div class="flex gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-400 mt-1.5"></div>
                            <div>
                                <p class="text-zinc-200">QC Verified</p>
                                <p class="text-xs text-zinc-500">{{ $order->qc_verified_at->format('d M Y, H:i') }} oleh {{ $order->qcVerifiedBy->name ?? '-' }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>