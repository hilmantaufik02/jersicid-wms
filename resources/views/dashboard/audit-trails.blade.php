<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Audit Trail - Semua Aktivitas') }}
            </h2>
            <p class="text-sm text-slate-400 mt-1">Log lengkap seluruh aktivitas sistem</p>
        </div>
    </x-slot>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs text-slate-400 border-b border-slate-800 bg-slate-800/30">
                        <th class="text-left py-3 px-4 font-medium">Waktu</th>
                        <th class="text-left py-3 px-4 font-medium">User</th>
                        <th class="text-left py-3 px-4 font-medium">Action</th>
                        <th class="text-left py-3 px-4 font-medium">Module</th>
                        <th class="text-left py-3 px-4 font-medium">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-3 px-4 text-xs text-slate-300">
                            {{ $activity->created_at->format('d M Y H:i') }}
                            <p class="text-[10px] text-slate-500">{{ $activity->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="py-3 px-4 text-sm text-white">{{ $activity->user?->name ?? 'System' }}</td>
                        <td class="py-3 px-4">
                            <span class="text-xs px-2 py-1 rounded bg-cyan-500/20 text-cyan-400 font-mono">
                                {{ $activity->action }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-300">{{ $activity->module }}</td>
                        <td class="py-3 px-4 text-xs text-slate-500 font-mono">{{ $activity->ip_address ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-slate-500">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-800/20">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</x-app-layout>