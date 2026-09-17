@extends('layouts.app')

@section('title', 'Master Data SKU')
@section('page-title', 'Master Data SKU')
@section('page-subtitle', 'Kelola data induk produk dan barcode.')

@section('content')
<div class="max-w-7xl mx-auto" x-data="masterSkuApp()">
    
    <!-- Top Action Bar -->
    <div class="gradient-card border border-slate-700 rounded-xl p-4 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
            <!-- Search & Filter -->
            <div class="flex-1 w-full flex gap-3">
                <div class="relative flex-1">
                    <input type="text" 
                           x-model="searchQuery"
                           @keydown.enter="applySearch"
                           placeholder="Cari SKU, Produk, atau Artikel..." 
                           class="w-full bg-slate-900 border border-slate-600 rounded-lg pl-10 pr-4 py-2 text-white focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <button @click="showFilterModal = true" 
                        class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter
                </button>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button @click="openCreateModal" 
                        class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Produk
                </button>
                
                <a href="{{ route('master-data.export') }}" 
                   class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition-all">
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
    </div>

    <!-- Products Grid View -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($products as $product)
        <div class="gradient-card border border-slate-700 rounded-xl p-6 hover:border-cyan-500/30 transition-all cursor-pointer group"
             @click="openProductDetail('{{ addslashes($product->product_name) }}')">
            
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-white mb-1 group-hover:text-cyan-400 transition-colors line-clamp-2">
                        {{ $product->product_name }}
                    </h3>
                    <p class="text-sm text-slate-400">{{ $product->article }}</p>
                </div>
                <span class="px-2 py-1 bg-cyan-900/30 text-cyan-400 rounded text-xs font-mono whitespace-nowrap ml-2">
                    {{ $product->total_variants }} Var
                </span>
            </div>

            <!-- Info Grid -->
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400">Kategori:</span>
                    <span class="text-white font-medium">{{ $product->size_category }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400">Ukuran:</span>
                    <span class="text-cyan-400 font-mono text-xs">{{ $product->available_sizes }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400">Harga:</span>
                    <span class="text-emerald-400 font-mono text-xs">
                        Rp {{ number_format($product->min_price) }} - {{ number_format($product->max_price) }}
                    </span>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex gap-2 pt-4 border-t border-slate-700">
                <button @click.stop="openProductDetail('{{ addslashes($product->product_name) }}')"
                        class="flex-1 px-3 py-2 bg-slate-700/50 hover:bg-slate-700 rounded-lg text-sm text-white transition-all">
                    Lihat Detail
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="text-slate-400">Belum ada data produk</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="mt-6">
        {{ $products->links() }}
    </div>
    @endif

    <!-- Product Detail Modal -->
    <div x-show="showProductDetailModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showProductDetailModal = false" class="fixed inset-0 bg-slate-900/80 transition-opacity"></div>
            <div class="relative bg-slate-800 border border-slate-700 rounded-2xl shadow-xl max-w-4xl w-full p-6 max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white" x-text="selectedProduct"></h3>
                        <p class="text-sm text-slate-400">Daftar semua varian SKU</p>
                    </div>
                    <button @click="showProductDetailModal = false" class="text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Variants Table -->
                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-slate-400 bg-slate-900/50">
                            <tr>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Size</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <template x-for="variant in productVariants" :key="variant.id">
                                <tr class="hover:bg-slate-900/30">
                                    <td class="px-4 py-3 font-mono text-cyan-400 text-xs" x-text="variant.sku"></td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 bg-cyan-900/30 text-cyan-400 rounded text-xs font-mono" x-text="variant.size"></span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-white text-xs" x-text="'Rp ' + variant.price.toLocaleString()"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 rounded text-xs font-bold"
                                              :class="variant.status === 'AKTIF' ? 'bg-emerald-900/30 text-emerald-400' : 'bg-red-900/30 text-red-400'"
                                              x-text="variant.status"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="editVariant(variant)" class="text-blue-400 hover:text-blue-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button @click="deleteVariant(variant)" class="text-red-400 hover:text-red-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showCreateModal || showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="closeModals" class="fixed inset-0 bg-slate-900/80 transition-opacity"></div>
            <div class="relative bg-slate-800 border border-slate-700 rounded-2xl shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white" x-text="showEditModal ? 'Edit SKU' : 'Tambah SKU Baru'"></h3>
                    <button @click="closeModals" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-1">SKU *</label>
                            <input type="text" x-model="formData.sku" required
                                   class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:border-cyan-400">
                        </div>
                        
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-1">Nama Produk *</label>
                            <input type="text" x-model="formData.product_name" required
                                   class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:border-cyan-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Artikel *</label>
                            <input type="text" x-model="formData.article" required
                                   class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:border-cyan-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Versi</label>
                            <input type="text" x-model="formData.version"
                                   class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:border-cyan-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Kategori Size *</label>
                            <select x-model="formData.size_category" required
                                    class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:border-cyan-400">
                                <option value="DEWASA">DEWASA</option>
                                <option value="KIDS">KIDS</option>
                                <option value="TEENS">TEENS</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Size *</label>
                            <input type="text" x-model="formData.size" required
                                   class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:border-cyan-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Harga (Rp) *</label>
                            <input type="number" x-model="formData.price" required min="0"
                                   class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:border-cyan-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Berat (gram) *</label>
                            <input type="number" x-model="formData.standard_weight_gram" required min="0"
                                   class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:border-cyan-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Min. Stok *</label>
                            <input type="number" x-model="formData.min_stock" required min="0"
                                   class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:border-cyan-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Status *</label>
                            <select x-model="formData.status" required
                                    class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:border-cyan-400">
                                <option value="AKTIF">AKTIF</option>
                                <option value="NONAKTIF">NONAKTIF</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="button" @click="closeModals" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium">Batal</button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-900 rounded-lg font-medium glow-cyan" x-text="showEditModal ? 'Update' : 'Simpan'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
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
                <form @submit.prevent="submitImport">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Pilih File Excel</label>
                        <input type="file" x-ref="importFile" accept=".xlsx,.xls,.csv" required
                               class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-cyan-500 file:text-slate-900 hover:file:bg-cyan-400">
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="showImportModal = false" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium">Batal</button>
                        <button type="submit" :disabled="isImporting" class="flex-1 px-4 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-900 rounded-lg font-medium glow-cyan disabled:opacity-50">
                            <span x-text="isImporting ? 'Mengupload...' : 'Upload & Import'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div x-show="showFilterModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showFilterModal = false" class="fixed inset-0 bg-slate-900/80 transition-opacity"></div>
            <div class="relative bg-slate-800 border border-slate-700 rounded-2xl shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Filter Produk</h3>
                    <button @click="showFilterModal = false" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Nama Produk</label>
                        <input type="text" x-model="filters.product_name" class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">SKU</label>
                        <input type="text" x-model="filters.sku" class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Artikel</label>
                        <input type="text" x-model="filters.article" class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Kategori</label>
                        <select x-model="filters.size_category" class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white">
                            <option value="">Semua</option>
                            <option value="DEWASA">DEWASA</option>
                            <option value="KIDS">KIDS</option>
                            <option value="TEENS">TEENS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Status</label>
                        <select x-model="filters.status" class="w-full px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white">
                            <option value="">Semua</option>
                            <option value="AKTIF">AKTIF</option>
                            <option value="NONAKTIF">NONAKTIF</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button @click="resetFilters" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg">Reset</button>
                    <button @click="applyFilters" class="flex-1 px-4 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-900 rounded-lg glow-cyan">Terapkan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function masterSkuApp() {
    return {
        searchQuery: '',
        showImportModal: false,
        showCreateModal: false,
        showEditModal: false,
        showProductDetailModal: false,
        showFilterModal: false,
        isImporting: false,
        selectedProduct: null,
        productVariants: [],
        editingId: null,
        formData: {
            sku: '',
            article: '',
            product_name: '',
            version: 'V1',
            sub_version: '',
            size_category: 'DEWASA',
            size: '',
            size_token: '',
            price: 0,
            standard_weight_gram: 250,
            min_stock: 0,
            status: 'AKTIF'
        },
        filters: {
            product_name: '',
            sku: '',
            article: '',
            size_category: '',
            status: ''
        },

        openCreateModal() {
            this.formData = {
                sku: '',
                article: '',
                product_name: '',
                version: 'V1',
                sub_version: '',
                size_category: 'DEWASA',
                size: '',
                size_token: '',
                price: 0,
                standard_weight_gram: 250,
                min_stock: 0,
                status: 'AKTIF'
            };
            this.showCreateModal = true;
        },

        closeModals() {
            this.showCreateModal = false;
            this.showEditModal = false;
        },

        async submitForm() {
            const url = this.showEditModal 
                ? `/master-data/${this.editingId}`
                : '/master-data';
            const method = this.showEditModal ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    window.location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        },

        async openProductDetail(productName) {
            this.selectedProduct = productName;
            try {
                const response = await fetch(`/master-data/variants/${encodeURIComponent(productName)}`);
                const result = await response.json();
                if (result.success) {
                    this.productVariants = result.data;
                    this.showProductDetailModal = true;
                }
            } catch (error) {
                alert('Gagal memuat detail produk: ' + error.message);
            }
        },

        editVariant(variant) {
            this.formData = { ...variant };
            this.editingId = variant.id;
            this.showEditModal = true;
            this.showProductDetailModal = false;
        },

        async deleteVariant(variant) {
            if (!confirm(`Yakin ingin menghapus SKU ${variant.sku}?`)) return;

            try {
                const response = await fetch(`/master-data/${variant.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    window.location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        },

        async submitImport() {
            const file = this.$refs.importFile.files[0];
            if (!file) {
                alert('Pilih file terlebih dahulu');
                return;
            }

            this.isImporting = true;

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch('/master-data/import', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    this.showImportModal = false;
                    window.location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                this.isImporting = false;
            }
        },

        applySearch() {
            const params = new URLSearchParams();
            if (this.searchQuery) params.append('search', this.searchQuery);
            window.location.href = '{{ route('master-data.index') }}?' + params.toString();
        },

        applyFilters() {
            const params = new URLSearchParams();
            if (this.filters.product_name) params.append('product_name', this.filters.product_name);
            if (this.filters.sku) params.append('sku', this.filters.sku);
            if (this.filters.article) params.append('article', this.filters.article);
            if (this.filters.size_category) params.append('size_category', this.filters.size_category);
            if (this.filters.status) params.append('status', this.filters.status);
            window.location.href = '{{ route('master-data.index') }}?' + params.toString();
        },

        resetFilters() {
            this.filters = { product_name: '', sku: '', article: '', size_category: '', status: '' };
            window.location.href = '{{ route('master-data.index') }}';
        }
    }
}
</script>
@endsection