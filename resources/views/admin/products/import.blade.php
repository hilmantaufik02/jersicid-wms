<x-admin-layout title="Import Produk">
    <x-slot name="pageTitle">Import Data Produk via Excel</x-slot>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="font-semibold">{{ session('success') }}</p>
                <a href="{{ route('products.index') }}" class="text-xs underline mt-1 inline-block">Lihat Daftar Produk →</a>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl text-sm">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <div>
                <p class="font-semibold mb-2">{{ session('error') }}</p>
                @if(session('import_errors'))
                <ul class="text-xs space-y-1 list-disc list-inside mt-2">
                    @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if(session('import_errors') && !session('error'))
    <div class="mb-6 p-4 bg-amber-500/10 border border-amber-500/20 text-amber-400 rounded-xl text-sm">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-semibold mb-2">⚠️ Ada beberapa baris yang gagal:</p>
                <ul class="text-xs space-y-1 list-disc list-inside max-h-40 overflow-y-auto">
                    @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if($errors->has('import_error'))
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl text-sm">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <div>
                <p class="font-semibold mb-2">❌ Import Gagal:</p>
                <pre class="whitespace-pre-wrap text-xs font-mono">{{ $errors->first('import_error') }}</pre>
            </div>
        </div>
    </div>
    @endif

    <div class="max-w-4xl mx-auto">
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-semibold">{{ session('success') }}</p>
                    <a href="{{ route('products.index') }}" class="text-xs underline mt-1 inline-block">Lihat Daftar Produk →</a>
                </div>
            </div>
        </div>
        @endif

        @if(session('import_errors'))
        <div class="mb-6 p-4 bg-amber-500/10 border border-amber-500/20 text-amber-400 rounded-xl text-sm">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold mb-2">⚠️ Ada beberapa baris yang gagal diimport:</p>
                    <ul class="text-xs space-y-1 list-disc list-inside max-h-40 overflow-y-auto">
                        @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        @if($errors->has('import_error'))
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl text-sm">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <div>
                    <p class="font-semibold mb-2">❌ Import Gagal:</p>
                    <pre class="whitespace-pre-wrap text-xs font-mono">{{ $errors->first('import_error') }}</pre>
                </div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl text-sm">
            <p class="font-semibold mb-2">❌ Error:</p>
            <ul class="text-xs space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Info Card -->
        <div class="glass-panel p-6 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-zinc-100 mb-2">Panduan Import Produk</h3>
                    <p class="text-sm text-zinc-400 mb-4">Import data produk secara massal menggunakan file Excel (.xlsx, .xls, .csv) dengan format yang sudah ditentukan.</p>

                    <div class="bg-zinc-900/50 rounded-lg p-4 mb-4">
                        <p class="text-xs font-semibold text-zinc-300 mb-2">Kolom yang Wajib Diisi:</p>
                        <ul class="text-xs text-zinc-400 space-y-1 list-disc list-inside">
                            <li><strong class="text-zinc-200">SKU_Induk</strong> - Kode unik produk (contoh: JRS-001)</li>
                            <li><strong class="text-zinc-200">Nama_Produk</strong> - Nama lengkap produk</li>
                        </ul>
                    </div>

                    <div class="bg-zinc-900/50 rounded-lg p-4">
                        <p class="text-xs font-semibold text-zinc-300 mb-2">Kolom Opsional:</p>
                        <ul class="text-xs text-zinc-400 space-y-1 list-disc list-inside">
                            <li><strong class="text-emerald-400">Harga_Dasar</strong> - Kosongkan saja (otomatis dari marketplace)</li>
                            <li><strong class="text-emerald-400">Harga_Modal</strong> - Kosongkan saja (otomatis dari marketplace)</li>
                            <li><strong class="text-zinc-200">Kategori</strong> - Nama kategori</li>
                            <li><strong class="text-zinc-200">Supplier</strong> - Nama supplier</li>
                            <li><strong class="text-zinc-200">Brand, Season, Tahun, Deskripsi</strong> - Informasi tambahan</li>
                            <li><strong class="text-zinc-200">Ukuran, Warna, Stok, Min_Stok</strong> - Data varian</li>
                        </ul>
                    </div>

                    <div class="bg-zinc-900/50 rounded-lg p-4">
                        <p class="text-xs font-semibold text-zinc-300 mb-2">Kolom Opsional:</p>
                        <ul class="text-xs text-zinc-400 space-y-1 list-disc list-inside">
                            <li><strong class="text-zinc-200">Kategori</strong> - Nama kategori (akan dibuat otomatis jika belum ada)</li>
                            <li><strong class="text-zinc-200">Supplier</strong> - Nama supplier (akan dibuat otomatis jika belum ada)</li>
                            <li><strong class="text-zinc-200">Brand, Season, Tahun, Deskripsi</strong> - Informasi tambahan produk</li>
                            <li><strong class="text-zinc-200">Ukuran, Warna, Stok, Min_Stok</strong> - Data varian produk</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Download Template & Upload Form -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Download Template -->
            <div class="glass-panel p-6">
                <h3 class="text-sm font-semibold text-zinc-100 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Template
                </h3>
                <p class="text-xs text-zinc-400 mb-4">Download file template Excel dengan contoh data untuk memudahkan Anda mengisi data produk.</p>
                <a href="{{ route('products.template') }}" class="block w-full bg-emerald-600 hover:bg-emerald-500 text-white text-center font-semibold py-3 rounded-xl transition-all shadow-lg shadow-emerald-500/20">
                    📥 Download Template Excel
                </a>
            </div>

            <!-- Upload Form -->
            <div class="glass-panel p-6">
                <h3 class="text-sm font-semibold text-zinc-100 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload File Excel
                </h3>
                <form action="{{ route('products.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Pilih File Excel</label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                            class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl px-4 py-3 text-sm text-zinc-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                        <p class="text-xs text-zinc-500 mt-2">Format: .xlsx, .xls, .csv (Maks. 10MB)</p>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 rounded-xl transition-all shadow-lg shadow-indigo-500/20">
                        🚀 Import Data
                    </button>
                </form>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-6">
            <a href="{{ route('products.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Produk
            </a>
        </div>
    </div>
</x-admin-layout>