<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard.gudang') }}"
                class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-all flex items-center gap-2 border border-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">Thermal Printer Management</h2>
                <p class="text-sm text-slate-400 mt-1">Zebra ZT411 - USB Mode - 95×150mm (4×8 Grid)</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6" x-data="printerApp()">

        @if (session('success'))
            <div
                class="p-4 bg-emerald-900/30 border border-emerald-500/50 text-emerald-400 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-900/30 border border-red-500/50 text-red-400 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Printer Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-slate-400 mb-1">Printer Model</p>
                <p class="text-lg font-bold text-white">Zebra ZT411</p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-slate-400 mb-1">Connection</p>
                <p class="text-lg font-bold text-cyan-400">USB</p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-slate-400 mb-1">Paper Size</p>
                <p class="text-lg font-bold text-white">95×150mm</p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-slate-400 mb-1">Layout</p>
                <p class="text-lg font-bold text-emerald-400">4×8 Grid</p>
            </div>
        </div>

        <!-- Print SKU Label -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                Cetak Label SKU (4×8 Grid)
            </h3>

            <form @submit.prevent="printLabels" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Pilih SKU</label>
                        <select x-model="selectedSku"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none transition-all">
                            <option value="">-- Pilih SKU --</option>
                            @foreach ($skus as $sku)
                                <option value="{{ $sku->sku }}">{{ $sku->sku }} - {{ $sku->product_name }}
                                    ({{ $sku->size }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Jumlah Label</label>
                        <input type="number" x-model="labelQty" min="1" max="32"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none transition-all">
                        <p class="text-xs text-slate-500 mt-1">Maks 32 label per sheet (4×8)</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="previewLabel" :disabled="!selectedSku || previewing"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 disabled:from-slate-700 disabled:to-slate-700 text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2">
                        <svg x-show="!previewing" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="previewing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="previewing ? 'Loading...' : 'Print Preview'"></span>
                    </button>

                    <button type="button" @click="downloadPdf" :disabled="!selectedSku || downloading"
                        class="flex-1 px-6 py-3 border border-slate-700 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 disabled:from-slate-700 disabled:to-slate-700 text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2">
                        <svg x-show="!downloading" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <svg x-show="downloading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="downloading ? 'Generating...' : 'Download PDF'"></span>
                    </button>
                </div>
            </form>

            <!-- ZPL Preview -->
            <div x-show="zplPreview" x-transition class="mt-4">
                <label class="block text-sm font-medium text-slate-300 mb-2">ZPL Code Preview</label>
                <textarea x-model="zplPreview" readonly rows="6"
                    class="w-full px-4 py-3 bg-slate-950 border border-slate-700 rounded-xl text-emerald-400 font-mono text-xs focus:outline-none"></textarea>
            </div>
        </div>

        <!-- Print Instructions -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Cara Print via USB (Zebra ZT411)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-slate-800/50 rounded-xl border border-slate-700/50">
                    <div class="w-8 h-8 bg-cyan-500/20 rounded-lg flex items-center justify-center mb-3">
                        <span class="text-lg font-bold text-cyan-400">1</span>
                    </div>
                    <h4 class="text-sm font-semibold text-white mb-2">Preview & Download PDF</h4>
                    <p class="text-xs text-slate-400">Klik "Print Preview" untuk lihat hasil, lalu "Download PDF"</p>
                </div>

                <div class="p-4 bg-slate-800/50 rounded-xl border border-slate-700/50">
                    <div class="w-8 h-8 bg-cyan-500/20 rounded-lg flex items-center justify-center mb-3">
                        <span class="text-lg font-bold text-cyan-400">2</span>
                    </div>
                    <h4 class="text-sm font-semibold text-white mb-2">Buka Zebra Setup Utilities</h4>
                    <p class="text-xs text-slate-400">Buka aplikasi Zebra Setup Utilities di komputer</p>
                </div>

                <div class="p-4 bg-slate-800/50 rounded-xl border border-slate-700/50">
                    <div class="w-8 h-8 bg-cyan-500/20 rounded-lg flex items-center justify-center mb-3">
                        <span class="text-lg font-bold text-cyan-400">3</span>
                    </div>
                    <h4 class="text-sm font-semibold text-white mb-2">Send File to Printer</h4>
                    <p class="text-xs text-slate-400">Pilih printer ZT411 dan send file PDF yang sudah didownload</p>
                </div>
            </div>
        </div>

        <!-- Printer Templates -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Template Label
                </h3>
                <button @click="openTemplateForm()"
                    class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-semibold rounded-xl transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Template
                </button>
            </div>

            <div class="space-y-3">
                @forelse($templates as $template)
                    <div
                        class="flex items-center justify-between p-4 bg-slate-800/50 rounded-xl border border-slate-700/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-cyan-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $template->name }}</p>
                                <p class="text-xs text-slate-400">{{ $template->type }} - {{ $template->width_mm }}mm x
                                    {{ $template->height_mm }}mm - {{ $template->dpi }} DPI</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="px-2 py-1 rounded-lg text-xs font-semibold {{ $template->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-400' }}">
                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <button
                                @click="editTemplate({{ $template->id }}, '{{ $template->name }}', '{{ $template->type }}', {{ $template->width_mm }}, {{ $template->height_mm }}, {{ $template->dpi }}, '{{ addslashes($template->zpl_code) }}', {{ $template->is_active ? 'true' : 'false' }})"
                                class="p-2 text-slate-400 hover:text-cyan-400 hover:bg-slate-700 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="deleteTemplate({{ $template->id }})"
                                class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500">
                        <p class="text-sm">Belum ada template printer</p>
                    </div>
                @endforelse
            </div>

            <!-- Template Form Modal -->
            <div x-show="showTemplateForm" x-cloak
                class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4"
                @click.self="showTemplateForm = false">
                <div
                    class="bg-slate-900 border border-slate-700 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b border-slate-700">
                        <h4 class="text-lg font-semibold text-white"
                            x-text="editingTemplateId ? 'Edit Template' : 'Tambah Template Baru'"></h4>
                    </div>

                    <form
                        :action="editingTemplateId ? `{{ url('gudang/printer/template') }}/${editingTemplateId}` :
                            '{{ route('gudang.printer.save-template') }}'"
                        method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="_method" :value="editingTemplateId ? 'PUT' : 'POST'">

                        @if ($errors->any())
                            <div class="p-3 bg-red-900/30 border border-red-500/50 text-red-400 rounded-lg text-sm">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Nama Template</label>
                                <input type="text" name="name" x-model="templateForm.name" required
                                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tipe Template</label>
                                <select name="type" x-model="templateForm.type" required
                                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none">
                                    <option value="SKU_LABEL">SKU Label</option>
                                    <option value="SHIPPING_LABEL">Shipping Label</option>
                                    <option value="BIN_LABEL">Bin Label</option>
                                    <option value="PO_LABEL">PO Label</option>
                                    <option value="CUSTOM">Custom</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Lebar (mm)</label>
                                <input type="number" name="width_mm" x-model="templateForm.width_mm" value="95"
                                    required
                                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tinggi (mm)</label>
                                <input type="number" name="height_mm" x-model="templateForm.height_mm"
                                    value="150" required
                                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-2">DPI</label>
                                <select name="dpi" x-model="templateForm.dpi" required
                                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none">
                                    <option value="203">203 DPI (Standard)</option>
                                    <option value="300">300 DPI (High Quality)</option>
                                    <option value="600">600 DPI (Ultra High)</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" x-model="templateForm.is_active"
                                        value="1"
                                        class="rounded border-slate-700 bg-slate-800 text-cyan-500 focus:ring-cyan-500/20">
                                    <span class="text-sm text-slate-300">Template Aktif</span>
                                </label>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-2">ZPL Code</label>
                                <textarea name="zpl_code" x-model="templateForm.zpl_code" rows="6" required placeholder="^XA^XZ"
                                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-emerald-400 font-mono text-sm focus:border-cyan-400 focus:outline-none"></textarea>
                                <p class="text-xs text-slate-500 mt-1">Gunakan variabel: {SKU}, {PRODUCT_NAME}, {SIZE},
                                    {QTY}</p>
                            </div>
                        </div>
                        <div class="flex gap-3 pt-4 border-t border-slate-700">
                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white font-semibold rounded-xl transition-all">
                                <span x-text="editingTemplateId ? 'Update Template' : 'Simpan Template'"></span>
                            </button>
                            <button type="button" @click="showTemplateForm = false"
                                class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-xl border border-slate-700 transition-all">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Preview Modal -->
    <div x-show="showPreview" x-cloak class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4"
        @click.self="showPreview = false">
        <div
            class="bg-slate-900 border border-slate-700 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-white">Print Preview - 95×150mm</h3>
                <button @click="showPreview = false" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Preview Content -->
            <div class="flex-1 overflow-auto p-6 bg-gray-100">
                <div id="preview-content" class="flex justify-center">
                    <!-- Preview will be injected here -->
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-between p-6 border-t border-slate-700 bg-slate-800">
                <div class="text-sm text-slate-400">
                    <span x-text="previewSku"></span> - <span x-text="previewProductName"></span>
                </div>
                <div class="flex gap-3">
                    <button @click="downloadPdf"
                        class="px-4 py-2 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-400 hover:to-sky-500 text-white rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download PDF
                    </button>
                    <button @click="showPreview = false"
                        class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printerApp() {
            return {
                selectedSku: '',
                labelQty: 1,
                previewing: false,
                printing: false,
                downloading: false,
                showPreview: false,
                showTemplateForm: false,
                zplPreview: '',
                previewSku: '',
                previewProductName: '',
                currentZpl: '',
                editingTemplateId: null,
                templateForm: {
                    name: '',
                    type: 'SKU_LABEL',
                    width_mm: 95,
                    height_mm: 150,
                    dpi: 300,
                    zpl_code: '',
                    is_active: true
                },

                async previewLabel() {
                    if (!this.selectedSku) {
                        alert('Pilih SKU terlebih dahulu');
                        return;
                    }

                    this.previewing = true;

                    try {
                        const response = await fetch('{{ route('gudang.printer.preview-label') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                sku: this.selectedSku,
                                qty: this.labelQty
                            })
                        });

                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Server tidak mengembalikan JSON.');
                        }

                        const data = await response.json();

                        if (data.success) {
                            // Tampilkan preview dengan QR code yang proper
                            document.getElementById('preview-content').innerHTML = `
                                <div style='display: flex; gap: 10mm; justify-content: center;'>
                                    <div>${data.html}</div>
                                    <div>${data.html}</div>
                                </div>
                            `;
                            this.previewSku = data.sku;
                            this.previewProductName = data.product_name;
                            this.currentZpl = data.zpl;
                            this.zplPreview = data.zpl;
                            this.showPreview = true;
                        } else {
                            alert('Error: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Preview error:', error);
                        alert('Error: ' + error.message);
                    } finally {
                        this.previewing = false;
                    }
                },

                async downloadPdf() {
                    if (!this.selectedSku) {
                        alert('Pilih SKU terlebih dahulu');
                        return;
                    }

                    this.downloading = true;

                    try {
                        const response = await fetch('{{ route('gudang.printer.download-pdf') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/pdf',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                sku: this.selectedSku,
                                qty: this.labelQty
                            })
                        });

                        if (response.ok) {
                            const blob = await response.blob();
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'label_' + this.selectedSku + '.pdf';
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);

                            alert('✅ PDF berhasil didownload!');
                        } else {
                            const errorData = await response.json();
                            alert('❌ ' + (errorData.message || 'Gagal download PDF'));
                        }
                    } catch (error) {
                        console.error('Download PDF error:', error);
                        alert('❌ Error: ' + error.message);
                    } finally {
                        this.downloading = false;
                    }
                },

                async printLabels() {
                    if (!this.selectedSku) {
                        alert('Pilih SKU terlebih dahulu');
                        return;
                    }

                    this.printing = true;

                    try {
                        const response = await fetch('{{ route('gudang.printer.print-sku') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                sku: this.selectedSku,
                                qty: this.labelQty
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert('✅ ' + data.message);
                        } else {
                            alert('❌ ' + data.message);
                        }
                    } catch (error) {
                        console.error('Print error:', error);
                        alert('❌ Error: ' + error.message);
                    } finally {
                        this.printing = false;
                    }
                },

                openTemplateForm() {
                    this.editingTemplateId = null;
                    this.templateForm = {
                        name: '',
                        type: 'SKU_LABEL',
                        width_mm: 95,
                        height_mm: 150,
                        dpi: 300,
                        zpl_code: '',
                        is_active: true
                    };
                    this.showTemplateForm = true;
                },

                editTemplate(id, name, type, width, height, dpi, zplCode, isActive) {
                    this.editingTemplateId = id;
                    this.templateForm = {
                        name: name,
                        type: type,
                        width_mm: width,
                        height_mm: height,
                        dpi: dpi,
                        zpl_code: zplCode,
                        is_active: isActive
                    };
                    this.showTemplateForm = true;
                },

                async deleteTemplate(id) {
                    if (!confirm('Hapus template ini?')) return;

                    try {
                        const url = '{{ route('gudang.printer.delete-template', ':id') }}'.replace(':id', id);
                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert(data.message);
                        }
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                }
            }
        }
    </script>
</x-app-layout>
