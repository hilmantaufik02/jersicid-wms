<x-admin-layout title="Retur & QC">
    <x-slot name="pageTitle">Manajemen Retur & Quality Control</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-primary">Retur Barang</h1>
            <p class="text-muted text-sm mt-1">Kelola pengembalian barang dari customer dan inspeksi QC</p>
        </div>
        <a href="{{ route('returns.create') }}" class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-2.5 rounded-xl flex items-center gap-2 text-sm font-semibold transition-all shadow-lg shadow-purple-500/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
            </svg>
            Buat Retur Baru
        </a>
    </div>

    @if(session('success'))
    <div class="alert-success px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif

    <div class="glass-panel overflow-hidden">
        <table class="precise-table">
            <thead>
                <tr>
                    <th>No. Retur</th>
                    <th>Invoice Asal</th>
                    <th>Customer</th>
                    <th>Total Refund</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $ret)
                <tr>
                    <td class="text-primary font-mono font-semibold">{{ $ret->return_number }}</td>
                    <td class="text-secondary">{{ $ret->transaction->invoice_number }}</td>
                    <td class="text-secondary">{{ $ret->customer_name }}</td>
                    <td class="text-cyan-400 font-bold">{{ format_rupiah($ret->refund_amount) }}</td>
                    <td>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-medium border
                            {{ $ret->status === 'approved' || $ret->status === 'refunded'
                                ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                                : ($ret->status === 'rejected'
                                    ? 'bg-red-500/10 text-red-400 border-red-500/20'
                                    : 'bg-amber-500/10 text-amber-400 border-amber-500/20') }}">
                            {{ ucfirst($ret->status) }}
                        </span>
                    </td>
                    <td class="text-right">
                        @if($ret->status === 'pending')
                        <a href="{{ route('returns.inspect', $ret->id) }}" class="text-cyan-400 hover:text-cyan-300 font-bold text-sm transition">🔍 Inspeksi QC</a>
                        @else
                        <span class="text-muted text-sm">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-muted">Belum ada data retur.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>