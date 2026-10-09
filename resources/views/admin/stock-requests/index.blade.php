<x-admin-layout title="Permintaan Stok">
    <x-slot name="pageTitle">Permintaan Stok & Produksi</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Permintaan Stok & Produksi</h1>
            <p class="text-sm text-muted">Kelola permintaan stok menipis dan produk baru</p>
        </div>
        @if(auth()->user()->role->slug !== 'cashier')
        <a href="{{ route('stock-requests.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Ajukan Permintaan
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="alert-success px-4 py-3 rounded-lg text-sm mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert-error px-4 py-3 rounded-lg text-sm mb-4">
        @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <!-- Filter -->
    <div class="glass-panel p-4 mb-6">
        <form action="{{ route('stock-requests.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <select name="status" class="form-input w-full">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Sedang Diajukan</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui - Ke Sublim</option>
                    <option value="sublimation_done" {{ request('status') == 'sublimation_done' ? 'selected' : '' }}>Selesai Sublim</option>
                    <option value="konveksi_done" {{ request('status') == 'konveksi_done' ? 'selected' : '' }}>Selesai Konveksi</option>
                    <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done di Gudang</option>
                </select>
            </div>
            <div>
                <select name="product" class="form-input w-full">
                    <option value="">Semua Produk</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-semibold flex-1">Filter</button>
                <a href="{{ route('stock-requests.index') }}" class="btn-ghost px-4 py-2 rounded-lg text-sm font-medium">Reset</a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        @php
        $statusCounts = [
        'pending' => $requests->where('status', 'pending')->count(),
        'approved' => $requests->where('status', 'approved')->count(),
        'sublimation_done' => $requests->where('status', 'sublimation_done')->count(),
        'konveksi_done' => $requests->where('status', 'konveksi_done')->count(),
        'done' => $requests->where('status', 'done')->count(),
        ];
        @endphp
        <div class="glass-panel p-4">
            <p class="text-xs text-muted">Total Permintaan</p>
            <p class="text-2xl font-bold text-primary">{{ $requests->total() }}</p>
        </div>
        <div class="glass-panel p-4">
            <p class="text-xs text-amber-400">Pending</p>
            <p class="text-2xl font-bold text-amber-400">{{ $statusCounts['pending'] }}</p>
        </div>
        <div class="glass-panel p-4">
            <p class="text-xs text-blue-400">Disetujui</p>
            <p class="text-2xl font-bold text-blue-400">{{ $statusCounts['approved'] }}</p>
        </div>
        <div class="glass-panel p-4">
            <p class="text-xs text-purple-400">Proses</p>
            <p class="text-2xl font-bold text-purple-400">{{ $statusCounts['sublimation_done'] + $statusCounts['konveksi_done'] }}</p>
        </div>
        <div class="glass-panel p-4">
            <p class="text-xs text-emerald-400">Selesai</p>
            <p class="text-2xl font-bold text-emerald-400">{{ $statusCounts['done'] }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="glass-panel overflow-hidden">
        <table class="precise-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Varian</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Diajukan</th>
                    <th>Oleh</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr>
                    <td>
                        <p class="font-medium text-primary">{{ $request->product->name }}</p>
                        <p class="text-xs text-muted">{{ $request->product->sku }}</p>
                    </td>
                    <td>
                        <p class="text-sm text-primary">{{ $request->variant->size }} / {{ $request->variant->color }}</p>
                        <p class="text-xs text-muted">Stok: {{ $request->variant->stock }}</p>
                    </td>
                    <td>
                        <span class="font-semibold text-primary">{{ $request->quantity_requested }}</span> pcs
                    </td>
                    <td>
                        @php [$badgeClass, $badgeLabel] = $request->status_badge; @endphp
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-medium border {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>
                    </td>
                    <td class="text-muted text-xs">{{ $request->created_at->format('d M Y') }}</td>
                    <td>
                        <p class="text-sm text-primary">{{ $request->requestedBy->name ?? '-' }}</p>
                    </td>
                    <td class="text-right">
                        <div class="flex justify-end gap-1">
                            <button type="button" onclick="openStatusModal({{ $request->id }}, '{{ $request->getNextStatusAttribute() }}')"
                                class="p-2 text-muted hover:text-indigo-400 hover:bg-indigo-500/10 rounded-lg transition"
                                title="Update Status"
                                @if(!$request->getNextStatusAttribute() || auth()->user()->role->slug === 'warehouse_staff') disabled @endif>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </button>

                            @if($request->status === 'pending' && auth()->user()->role->slug !== 'warehouse_staff')
                            <form action="{{ route('stock-requests.approve', $request) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-muted hover:text-emerald-400 hover:bg-emerald-500/10 rounded-lg transition" title="Setujui">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif

                            @if($request->status === 'pending' && $request->requested_by === auth()->id())
                            <form action="{{ route('stock-requests.destroy', $request) }}" method="POST" class="inline" onsubmit="return confirm('Hapus permintaan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-muted hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-10 text-muted">Tidak ada permintaan stok.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $requests->links() }}</div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="glass-panel p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold text-primary mb-3">Update Status Permintaan</h3>
            <form id="statusForm" method="POST">
                @csrf
                <input type="hidden" name="status" id="newStatus">
                <div class="mb-4">
                    <p class="text-sm text-muted">Konfirmasi update status permintaan ini?</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="closeStatusModal()" class="btn-ghost flex-1 py-2 rounded-lg">Batal</button>
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white py-2 rounded-lg font-semibold">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

@push('scripts')
<script>
    function openStatusModal(id, status) {
        document.getElementById('statusForm').action = '/stock-requests/' + id + '/status';
        document.getElementById('newStatus').value = status;
        document.getElementById('statusModal').classList.remove('hidden');
        document.getElementById('statusModal').classList.add('flex');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
        document.getElementById('statusModal').classList.remove('flex');
    }
</script>
@endpush