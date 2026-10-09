<x-admin-layout title="Detail Approval">
    <x-slot name="pageTitle">Detail Persetujuan</x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="glass-panel p-6 mb-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-zinc-100">{{ ucfirst(str_replace('_', ' ', $approval->approval_type)) }}</h2>
                        <p class="text-sm text-zinc-500 mt-0.5">ID: #{{ $approval->id }} • {{ $approval->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                <span class="px-3 py-1.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold uppercase">
                    {{ $approval->status }}
                </span>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-4 p-4 bg-zinc-900/50 rounded-lg">
                <div>
                    <p class="text-xs text-zinc-500 mb-1">Diajukan Oleh</p>
                    <p class="text-sm font-medium text-zinc-200">{{ $approval->requestedBy->name }}</p>
                    <p class="text-xs text-zinc-500">{{ $approval->requestedBy->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 mb-1">Tipe Referensi</p>
                    <p class="text-sm font-medium text-zinc-200">{{ $approval->reference_type }}</p>
                    <p class="text-xs text-zinc-500">ID: {{ $approval->reference_id }}</p>
                </div>
            </div>

            <!-- Reason -->
            <div class="mt-4">
                <p class="text-xs text-zinc-500 mb-2">Alasan Permintaan</p>
                <p class="text-sm text-zinc-300 bg-zinc-900/50 p-3 rounded-lg">{{ $approval->reason }}</p>
            </div>
        </div>

        <!-- Reference Data (Jika ada) -->
        @if($referenceData)
        <div class="glass-panel p-6 mb-6">
            <h3 class="text-sm font-semibold text-zinc-100 mb-4">Data Referensi</h3>
            <div class="bg-zinc-900/50 rounded-lg p-4 text-sm text-zinc-400">
                @if($approval->reference_type === 'user_activation')
                <div class="space-y-2">
                    <p><span class="text-zinc-500">Nama:</span> <span class="text-zinc-200">{{ $referenceData->name }}</span></p>
                    <p><span class="text-zinc-500">Email:</span> <span class="text-zinc-200">{{ $referenceData->email }}</span></p>
                    <p><span class="text-zinc-500">Role:</span> <span class="text-zinc-200">{{ $referenceData->role->name }}</span></p>
                    <p><span class="text-zinc-500">Status:</span>
                        <span class="{{ $referenceData->is_active ? 'text-emerald-400' : 'text-amber-400' }}">
                            {{ $referenceData->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </p>
                </div>
                @elseif($approval->reference_type === 'stock_opname')
                <div class="space-y-2">
                    <p><span class="text-zinc-500">Produk:</span> <span class="text-zinc-200">{{ $referenceData->variant->product->name }}</span></p>
                    <p><span class="text-zinc-500">Varian:</span> <span class="text-zinc-200">{{ $referenceData->variant->size }} / {{ $referenceData->variant->color }}</span></p>
                    <p><span class="text-zinc-500">Stok Sistem:</span> <span class="text-zinc-200">{{ $referenceData->system_stock }}</span></p>
                    <p><span class="text-zinc-500">Stok Fisik:</span> <span class="text-zinc-200">{{ $referenceData->actual_stock }}</span></p>
                    <p><span class="text-zinc-500">Selisih:</span>
                        <span class="{{ $referenceData->difference < 0 ? 'text-red-400' : 'text-emerald-400' }}">
                            {{ $referenceData->difference > 0 ? '+' : '' }}{{ $referenceData->difference }}
                        </span>
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Action Buttons (Hanya jika pending) -->
        @if($approval->status === 'pending')
        <div class="glass-panel p-6">
            <h3 class="text-sm font-semibold text-zinc-100 mb-4">Tindakan</h3>

            <!-- Approve Form -->
            <form action="{{ route('approvals.approve', $approval) }}" method="POST" class="mb-4">
                @csrf
                <div class="mb-3">
                    <label class="text-xs text-zinc-500 mb-1 block">Catatan (Opsional)</label>
                    <textarea name="reason" rows="2" class="w-full bg-zinc-900/50 border border-zinc-800 rounded-lg px-3 py-2 text-sm text-zinc-200 focus:outline-none focus:border-emerald-500" placeholder="Tambahkan catatan...">{{ $approval->reason }}</textarea>
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-2.5 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Setujui Permintaan
                </button>
            </form>

            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-zinc-800"></div>
                </div>
                <div class="relative flex justify-center text-xs"><span class="bg-[#121214] px-3 text-zinc-600">atau</span></div>
            </div>

            <!-- Reject Form -->
            <form action="{{ route('approvals.reject', $approval) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="text-xs text-zinc-500 mb-1 block">Alasan Penolakan <span class="text-red-400">*</span></label>
                    <textarea name="rejection_reason" rows="2" required class="w-full bg-zinc-900/50 border border-zinc-800 rounded-lg px-3 py-2 text-sm text-zinc-200 focus:outline-none focus:border-red-500" placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>
                <button type="submit" class="w-full bg-zinc-800 hover:bg-red-600 text-zinc-300 hover:text-white font-semibold py-2.5 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Tolak Permintaan
                </button>
            </form>
        </div>
        @else
        <!-- Status Info (Jika sudah diproses) -->
        <div class="glass-panel p-6">
            <div class="flex items-center gap-3 p-4 rounded-lg {{ $approval->status === 'approved' ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-red-500/10 border border-red-500/20' }}">
                <div class="w-10 h-10 rounded-lg {{ $approval->status === 'approved' ? 'bg-emerald-500/20' : 'bg-red-500/20' }} flex items-center justify-center">
                    @if($approval->status === 'approved')
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    @else
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-medium {{ $approval->status === 'approved' ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $approval->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                    </p>
                    <p class="text-xs text-zinc-500">
                        Oleh {{ $approval->approvedBy->name }} • {{ $approval->updated_at->format('d M Y H:i') }}
                    </p>
                    @if($approval->rejection_reason)
                    <p class="text-xs text-zinc-400 mt-1">Alasan: {{ $approval->rejection_reason }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Back Button -->
        <div class="mt-6">
            <a href="{{ route('approvals.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Approval
            </a>
        </div>
    </div>
</x-admin-layout>