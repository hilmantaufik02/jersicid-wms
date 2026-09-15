@extends('layouts.app')
@section('page-title', 'Master Data SKU')
@section('page-subtitle', 'Kelola data induk produk dan barcode.')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Action Bar -->
    <div class="bg-wms-card border border-wms-border rounded-xl p-4 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('master-data.index') }}" method="GET" class="flex-1 w-full md:w-auto">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari SKU, Nama Barang, atau Artikel..." 
                       class="w-full bg-wms-bg border border-wms-border rounded-lg pl-10 pr-4 py-2 text-white focus:border-wms-accent">
                <svg class="w-5 h-5 absolute left-3 top-2.5 text-wms-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </form>
        
        <div class="flex gap-3">
            <a href="{{ route('master-data.export') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </a>
            <form action="{{ route('master-data.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" required class="hidden" id="excel-file" accept=".xlsx,.xls,.csv">
                <button type="button" onclick="document.getElementById('excel-file').click()" class="bg-wms-accent hover:bg-wms-accent-hover text-slate-900 px-4 py-2 rounded-lg font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-wms-card border border-wms-border rounded-xl overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="text-xs uppercase text-wms-muted bg-wms-bg/50 border-b border-wms-border">
                <tr>
                    <th class="px-6 py-3">SKU</th>
                    <th class="px-6 py-3">Nama Produk</th>
                    <th class="px-6 py-3">Artikel</th>
                    <th class="px-6 py-3">Size</th>
                    <th class="px-6 py-3 text-right">Harga</th>
                    <th class="px-6 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-wms-border/50">
                @forelse($skus as $sku)
                <tr class="hover:bg-wms-bg/30 transition-colors">
                    <td class="px-6 py-4 font-mono text-wms-accent font-bold">{{ $sku->sku }}</td>
                    <td class="px-6 py-4 text-white">{{ $sku->product_name }}</td>
                    <td class="px-6 py-4 text-wms-muted">{{ $sku->article }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-slate-700 rounded text-xs font-mono">{{ $sku->size }}</span>
                    </td>
                    <td class="px-6 py-4 text-right font-mono text-white">Rp {{ number_format($sku->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $sku->status === 'AKTIF' ? 'bg-emerald-900/30 text-emerald-400 border border-emerald-500/30' : 'bg-red-900/30 text-red-400 border border-red-500/30' }}">
                            {{ $sku->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-wms-muted italic">Tidak ada data SKU ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-wms-border bg-wms-bg/30">
            {{ $skus->links() }}
        </div>
    </div>
</div>
@endsection