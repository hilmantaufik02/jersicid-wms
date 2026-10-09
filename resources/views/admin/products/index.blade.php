<x-admin-layout title="Produk">
    <x-slot name="pageTitle">Manajemen Produk Jersey</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary tracking-tight">Manajemen Produk Jersey</h1>
            <p class="text-muted text-sm mt-1">Kelola semua produk jersey dan variannya</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('products.import') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Import Excel
            </a>
            <a href="{{ route('products.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Produk
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filter Bar -->
    <div class="glass-panel p-4 mb-6">
        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label class="form-label">Cari Produk</label>
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, SKU, atau Brand..." class="form-input w-full pl-10">
                </div>
            </div>
            <div>
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-input w-full">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Status Stok</label>
                <select name="stock_status" class="form-input w-full">
                    <option value="">Semua Stok</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>
            <div class="md:col-span-4 flex gap-3 mt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-lg transition-all">🔍 Terapkan Filter</button>
                <a href="{{ route('products.index') }}" class="px-6 py-2.5 btn-ghost text-sm font-medium rounded-lg transition-all">Reset Filter</a>
            </div>
        </form>
    </div>

    <div class="glass-panel overflow-hidden">
        <!-- Table -->
        <div class="glass-panel overflow-hidden">
            <table class="precise-table">
                <thead>
                    <tr>
                        <th>PRODUK</th>
                        <th>SKU</th>
                        <th>KATEGORI</th>
                        <th>HARGA DASAR</th>
                        <th>TOTAL STOK</th>
                        <th>STATUS</th>
                        <th class="text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($product->image && Storage::disk('public')->exists($product->image))
                                <img src="{{ asset('storage/' . $product->image) }}"
                                    class="w-12 h-12 rounded-lg object-cover"
                                    alt="{{ $product->name }}">
                                @else
                                <div class="w-12 h-12 rounded-lg bg-elevated flex items-center justify-center">
                                    <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <p class="font-medium text-primary">{{ $product->name }}</p>
                                    <p class="text-xs text-muted">{{ $product->brand ?? 'JERSIC.ID' }} {{ $product->year ?? '2025' }}</p>
                                </div>
                            </div>
                        </td>
                        <!-- SKU Column -->
                        <td>
                            <div class="font-mono text-sm">
                                <span class="text-primary">{{ $product->sku }}</span>
                                @if($product->variants->count() > 1)
                                <span class="text-xs text-muted block">
                                    {{ $product->variants->count() }} varian
                                </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($product->category)
                            <span class="px-2 py-1 rounded-md text-xs font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                {{ $product->category->name }}
                            </span>
                            @else
                            <span class="text-xs text-muted">-</span>
                            @endif
                        </td>
                        <td class="font-semibold text-primary">
                            @if($product->base_price > 0)
                            Rp {{ number_format($product->base_price, 0, ',', '.') }}
                            @else
                            <span class="text-muted text-xs italic">Dari Marketplace</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm text-primary">{{ number_format($product->variants->sum('stock')) }} pcs</span>
                            @if($product->variants->sum('stock') <= $product->variants->sum('min_stock'))
                                <span class="text-xs text-amber-400 ml-1">(Low)</span>
                                @endif
                        </td>
                        <td>
                            <span class="px-2 py-1 rounded-md text-xs font-medium {{ $product->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('products.edit', $product) }}"
                                    class="p-2 text-muted hover:text-indigo-400 hover:bg-indigo-500/10 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus produk {{ $product->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-muted hover:text-red-400 hover:bg-red-500/10 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10">
                            <div class="flex flex-col items-center justify-center text-muted">
                                <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="text-sm">Belum ada produk</p>
                                <p class="text-xs mt-1">Import Excel atau tambah produk baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 px-4">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
</x-admin-layout>