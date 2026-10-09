<x-admin-layout title="Approvals">
    <x-slot name="pageTitle">Manajemen Persetujuan</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Pending Approvals (Main) -->
        <div class="lg:col-span-2">
            <div class="glass-panel p-6 mb-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-100">Menunggu Persetujuan</h3>
                        <p class="text-xs text-zinc-500 mt-0.5">{{ $approvals->total() }} request pending</p>
                    </div>
                    @if($approvals->total() > 0)
                    <span class="px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-medium">
                        Perlu Tindakan
                    </span>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($approvals as $approval)
                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-4 hover:border-zinc-700 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-zinc-100">{{ ucfirst(str_replace('_', ' ', $approval->approval_type)) }}</p>
                                    <p class="text-xs text-zinc-500">Diajukan oleh {{ $approval->requestedBy->name }} • {{ $approval->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 rounded-md bg-amber-500/10 text-amber-400 text-[10px] font-medium uppercase">Pending</span>
                        </div>

                        <p class="text-xs text-zinc-400 mb-3 line-clamp-2">{{ $approval->reason }}</p>

                        <div class="flex gap-2">
                            <a href="{{ route('approvals.show', $approval) }}" class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-medium py-2 rounded-lg transition-colors text-center">
                                Review Detail
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-zinc-400">Tidak ada approval yang menunggu.</p>
                        <p class="text-xs text-zinc-600 mt-1">Semua request telah diproses.</p>
                    </div>
                    @endforelse
                </div>

                @if($approvals->hasPages())
                <div class="mt-6">
                    {{ $approvals->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- My Requests (Sidebar) -->
        <div class="lg:col-span-1">
            <div class="glass-panel p-6">
                <h3 class="text-sm font-semibold text-zinc-100 mb-4">Request Saya</h3>
                <div class="space-y-3">
                    @forelse($myApprovals as $myApproval)
                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-medium text-zinc-200">{{ ucfirst(str_replace('_', ' ', $myApproval->approval_type)) }}</p>
                            <span class="px-2 py-0.5 rounded text-[10px] font-medium
                                    {{ $myApproval->status === 'approved' ? 'bg-emerald-500/10 text-emerald-400' : ($myApproval->status === 'rejected' ? 'bg-red-500/10 text-red-400' : 'bg-amber-500/10 text-amber-400') }}">
                                {{ ucfirst($myApproval->status) }}
                            </span>
                        </div>
                        <p class="text-[10px] text-zinc-500">{{ $myApproval->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-zinc-500 text-center py-4">Belum ada request.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>