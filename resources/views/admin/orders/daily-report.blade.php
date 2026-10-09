<x-admin-layout title="Laporan Harian">
    <x-slot name="pageTitle">Laporan Harian Fulfillment</x-slot>

    <!-- Date Filter -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Laporan Harian</h1>
            <p class="text-sm text-zinc-500 mt-1">Rekapitulasi order, pengiriman, dan retur</p>
        </div>
        <form action="{{ route('orders.daily-report') }}" method="GET" class="flex gap-3">
            <input type="date" name="date" value="{{ $date }}" class="bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all">Tampilkan</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="glass-panel p-5">
            <p class="text-xs text-zinc-500 uppercase">Total Order Hari Ini</p>
            <p class="text-3xl font-bold text-zinc-100 mt-2">{{ $orders->count() }}</p>
        </div>
        <div class="glass-panel p-5">
            <p class="text-xs text-zinc-500 uppercase">Total Barang Keluar</p>
            <p class="text-3xl font-bold text-indigo-400 mt-2">{{ $orders->sum(fn($o) => $o->total_items) }} pcs</p>
        </div>
        <div class="glass-panel p-5">
            <p class="text-xs text-zinc-500 uppercase">Sudah Pickup</p>
            <p class="text-3xl font-bold text-emerald-400 mt-2">{{ $orders->where('status', 'shipped')->count() }}</p>
        </div>
        <div class="glass-panel p-5">
            <p class="text-xs text-zinc-500 uppercase">Retur Masuk</p>
            <p class="text-3xl font-bold text-red-400 mt-2">{{ $returns }} order</p>
        </div>
    </div>

    <!-- Rekap per Ekspedisi -->
    <div class="glass-panel p-6 mb-6">
        <h3 class="text-sm font-semibold text-zinc-100 mb-4">📦 Rekap Pengiriman per Ekspedisi</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-800 precise-table">
                <thead class="bg-zinc-900/50">
                    <tr>
                        <th>Ekspedisi</th>
                        <th>Jumlah Paket</th>
                        <th>Total Item (pcs)</th>
                        <th>Estimasi Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-[#121214]">
                    @forelse($byCourier as $courier => $data)
                    <tr>
                        <td class="font-medium text-zinc-100">{{ $courier ?? 'Belum Ada Ekspedisi' }}</td>
                        <td class="text-zinc-300">{{ $data['count'] }} paket</td>
                        <td class="text-zinc-300">{{ $data['items'] }} pcs</td>
                        <td class="text-emerald-400 font-semibold">{{ format_rupiah($data['revenue']) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-zinc-500">Belum ada data pengiriman hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detail Semua Order -->
    <div class="glass-panel p-6">
        <h3 class="text-sm font-semibold text-zinc-100 mb-4">📋 Detail Semua Order</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-800 precise-table">
                <thead class="bg-zinc-900/50">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Ekspedisi</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="bg-[#121214]">
                    @forelse($orders as $order)
                    <tr>
                        <td class="font-mono text-zinc-100">{{ $order->order_number }}</td>
                        <td class="text-zinc-300">{{ $order->courier_name ?? '-' }}</td>
                        <td class="text-zinc-300">{{ $order->customer_name }}</td>
                        <td class="text-zinc-300">{{ $order->total_items }} pcs</td>
                        <td>
                            @php [$badgeClass, $badgeLabel] = $order->status_badge; @endphp
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-medium border {{ $badgeClass }}">{{ $badgeLabel }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-zinc-500">Tidak ada order pada tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>