<x-admin-layout title="Order Fulfillment">
    <x-slot name="pageTitle">Order Fulfillment</x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="glass-panel p-4">
            <p class="text-xs text-zinc-500 uppercase">Belum Diproses</p>
            <p class="text-2xl font-bold text-zinc-100 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="glass-panel p-4">
            <p class="text-xs text-zinc-500 uppercase">Picking</p>
            <p class="text-2xl font-bold text-blue-400 mt-1">{{ $stats['picking'] }}</p>
        </div>
        <div class="glass-panel p-4">
            <p class="text-xs text-zinc-500 uppercase">Siap Packing</p>
            <p class="text-2xl font-bold text-amber-400 mt-1">{{ $stats['ready_packing'] }}</p>
        </div>
        <div class="glass-panel p-4">
            <p class="text-xs text-zinc-500 uppercase">Siap Pickup</p>
            <p class="text-2xl font-bold text-emerald-400 mt-1">{{ $stats['ready_pickup'] }}</p>
        </div>
        <div class="glass-panel p-4">
            <p class="text-xs text-zinc-500 uppercase">Shipped Today</p>
            <p class="text-2xl font-bold text-indigo-400 mt-1">{{ $stats['shipped_today'] }}</p>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex gap-2">
            @php
            $statuses = [
            'all' => 'Semua',
            'pending' => 'Belum Diproses',
            'picking' => 'Picking',
            'ready_packing' => 'Siap Packing',
            'ready_pickup' => 'Siap Pickup',
            'shipped' => 'Sudah Kirim',
            ];
            @endphp
            @foreach($statuses as $key => $label)
            <a href="{{ route('orders.index', ['status' => $key]) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $status === $key ? 'bg-indigo-600 text-white' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
        <div class="flex gap-3">
            <a href="{{ route('orders.daily-report') }}" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                📊 Laporan Harian
            </a>
            <a href="{{ route('orders.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-lg shadow-indigo-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Input Order Baru
            </a>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="glass-panel overflow-hidden">
        <table class="min-w-full divide-y divide-zinc-800 precise-table">
            <thead class="bg-zinc-900/50">
                <tr>
                    <th>No. Pesanan</th>
                    <th>No. Resi</th>
                    <th>Ekspedisi</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-[#121214]">
                @forelse($orders as $order)
                <tr>
                    <td class="font-mono text-zinc-100 font-medium">{{ $order->order_number }}</td>
                    <td class="font-mono text-zinc-400 text-xs">{{ $order->tracking_number ?? '-' }}</td>
                    <td>
                        @if($order->courier_name)
                        <span class="px-2 py-1 rounded bg-zinc-800 text-zinc-300 text-xs font-medium">
                            {{ $order->courier_name }}
                        </span>
                        @else
                        <span class="text-zinc-600 text-xs">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="text-sm text-zinc-200">{{ $order->customer_name }}</div>
                        <div class="text-xs text-zinc-500">{{ $order->customer_phone }}</div>
                    </td>
                    <td class="text-sm text-zinc-300">{{ $order->total_items }} pcs</td>
                    <td class="text-sm text-emerald-400 font-semibold">{{ format_rupiah($order->total) }}</td>
                    <td>
                        @php [$badgeClass, $badgeLabel] = $order->status_badge; @endphp
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-medium border {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>
                    </td>
                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('orders.show', $order) }}" class="p-1.5 rounded-lg text-zinc-400 hover:text-indigo-400 hover:bg-indigo-500/10 transition" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="p-1.5 rounded-lg text-zinc-400 hover:text-emerald-400 hover:bg-emerald-500/10 transition" title="Print Struk">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                            </a>
                            @if($order->status === 'pending')
                            <form action="{{ route('orders.start-picking', $order) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-lg text-zinc-400 hover:text-blue-400 hover:bg-blue-500/10 transition" title="Mulai Picking">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                            @if(!in_array($order->status, ['shipped', 'delivered', 'cancelled']))
                            <div class="flex gap-2">
                                @if(auth()->user()->role->slug !== 'warehouse_staff')
                                <a href="{{ route('orders.edit', $order) }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                @endif

                                <button type="button" onclick="openCancelModal()" class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Batal
                                </button>
                            </div>
                            @endif

                            <!-- Cancel Modal -->
                            <div id="cancelModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
                                <div class="glass-panel p-6 max-w-md w-full mx-4">
                                    <h3 class="text-lg font-bold text-primary mb-3">Batalkan Order #{{ $order->order_number }}</h3>
                                    <form action="{{ route('orders.cancel', $order) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="form-label">Alasan Pembatalan</label>
                                            <textarea name="reason" rows="4" required class="form-input w-full" placeholder="Contoh: Customer meminta pembatalan via chat..."></textarea>
                                            @error('reason') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="alert-warning px-3 py-2 rounded-lg text-xs mb-4">
                                            ⚠️ Stok yang sudah di-pick ({{ $order->items->sum('picked_quantity') }} items) akan dikembalikan ke inventory otomatis.
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" onclick="closeCancelModal()" class="btn-ghost flex-1 py-2 rounded-lg">Batal</button>
                                            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-500 text-white py-2 rounded-lg font-semibold">
                                                Ya, Batalkan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @push('scripts')
                            <script>
                                function openCancelModal() {
                                    document.getElementById('cancelModal').classList.remove('hidden');
                                    document.getElementById('cancelModal').classList.add('flex');
                                }

                                function closeCancelModal() {
                                    document.getElementById('cancelModal').classList.add('hidden');
                                    document.getElementById('cancelModal').classList.remove('flex');
                                }
                            </script>
                            @endpush
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center text-zinc-500 text-sm">
                        Belum ada order. Klik "Input Order Baru" untuk membuat order pertama.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</x-admin-layout>