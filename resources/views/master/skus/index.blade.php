@extends('layouts.app')

@section('title', 'Master Data SKU')
@section('page-title', 'Master Data SKU')
@section('page-subtitle', 'Kelola data induk produk dan barcode.')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ showImportModal: false }">
    
    <!-- Action Bar -->
    <div class="gradient-card border border-slate-700 rounded-xl p-4 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('master-data.index') }}" method="GET" class="flex-1 w-full md:w-auto">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari SKU, Nama Barang, atau Artikel..." 
                       class="w-full bg-slate-900 border border-slate-600 rounded-lg pl-10 pr-4 py-2 text-white focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400">
                <svg class="w-5 h-5 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </form>
        
        <div class="flex gap-3">
            <a href="{{ route('master-data.export') }}" 
               class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export
            </a>
            
            <button @click="showImportModal = true" 
                    class="bg-cyan-500 hover:bg-cyan-400 text-slate-900 px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition-all glow-cyan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Import
            </button>
        </div>
    </div>

    {{-- Import Failures Display --}}
    @if(session('import_failures'))
    <div class="mb-6 p-4 bg-red-900/30 border border-red-500/50 rounded-lg">
        <h4 class="text-red-400 font-semibold mb-2 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Detail Kegagalan Import
        </h4>
        <ul class="text-sm text-red-300 space-y-1 max-h-40 overflow-y-auto">
            @foreach(session('import_failures') as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Import Modal --}}
    <div x-show="showImportModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showImportModal = false" class="fixed inset-0 bg-slate-900/80 transition-opacity"></div>

            <div class="relative bg-slate-800 border border-slate-700 rounded-2xl shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Import Master SKU</h3>
                    <button @click="showImportModal = false" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-6 p-4 bg-cyan-900/20 border border-cyan-500/30 rounded-lg">
                    <p class="text-sm text-cyan-400 mb-2 font-semibold">Format File:</p>
                    <ul class="text-xs text-slate-300 space-y-1">
                        <li>• Excel (.xlsx, .xls) atau CSV</li>
                        <li>• Maksimal ukuran: 10MB</li>
                        <li>• Kolom wajib: SKU, Product Name, Size Category, Size</li>
                    </ul>
                </div>

                <form action="{{ route('master-data.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Pilih File Excel</label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                               class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-cyan-500 file:text-slate-900 hover:file:bg-cyan-400">
                        @error('file')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="button" @click="showImportModal = false"
                                class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-900 rounded-lg font-medium transition-all glow-cyan">
                            Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="gradient-card border border-slate-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs uppercase text-slate-400 bg-slate-900/50 border-b border-slate-700">
                    <tr>
                        <th class="px-6 py-3">SKU</th>
                        <th class="px-6 py-3">Nama Produk</th>
                        <th class="px-6 py-3">Artikel</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Size</th>
                        <th class="px-6 py-3 text-right">Harga</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($skus as $sku)
                    <tr class="hover:bg-slate-900/30 transition-colors">
                        <td class="px-6 py-4 font-mono text-cyan-400 font-bold">{{ $sku->sku }}</td>
                        <td class="px-6 py-4 text-white">{{ $sku->product_name }}</td>
                        <td class="px-6 py-4 text-slate-400">{{ $sku->article }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-slate-700 rounded text-xs">{{ $sku->size_category }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-cyan-900/30 text-cyan-400 rounded text-xs font-mono">{{ $sku->size }}</span>
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
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">
                            Tidak ada data SKU. Silakan import atau tambah data baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-700 bg-slate-900/30">
            {{ $skus->links() }}
        </div>
    </div>
</div>
@endsection