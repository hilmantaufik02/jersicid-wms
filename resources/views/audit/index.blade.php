@extends('layouts.app')

@section('title', 'Audit Trail')
@section('page-title', 'Audit Trail')
@section('page-subtitle', 'Log aktivitas sistem')

@section('content')
<div class="bg-wms-card border border-wms-border rounded-xl overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="text-xs uppercase text-wms-muted bg-wms-bg/50">
            <tr>
                <th class="px-6 py-3">Waktu</th>
                <th class="px-6 py-3">User</th>
                <th class="px-6 py-3">Action</th>
                <th class="px-6 py-3">Module</th>
                <th class="px-6 py-3">IP Address</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-wms-border/50">
            @forelse($audits as $audit)
            <tr class="hover:bg-wms-bg/30">
                <td class="px-6 py-4 text-wms-muted">{{ $audit->created_at->format('d/m/Y H:i:s') }}</td>
                <td class="px-6 py-4 text-white">{{ $audit->user?->name ?? 'System' }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs font-bold 
                        {{ $audit->action === 'CREATE' ? 'bg-emerald-900/30 text-emerald-400' : 
                           ($audit->action === 'DELETE' ? 'bg-red-900/30 text-red-400' : 
                           'bg-blue-900/30 text-blue-400') }}">
                        {{ $audit->action }}
                    </span>
                </td>
                <td class="px-6 py-4 text-wms-accent">{{ $audit->module }}</td>
                <td class="px-6 py-4 text-wms-muted font-mono text-xs">{{ $audit->ip_address }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-wms-muted">Belum ada audit trail</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-wms-border">
        {{ $audits->links() }}
    </div>
</div>
@endsection