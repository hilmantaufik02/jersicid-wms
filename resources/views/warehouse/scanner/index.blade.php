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
                <h2 class="font-semibold text-xl text-white leading-tight">Barcode Scanner</h2>
                <p class="text-sm text-slate-400 mt-1">Scan barcode dengan buffer system</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6" x-data="scannerApp()">

        <!-- Scanner Status & Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-slate-400">Scanner Status</p>
                    <div class="w-3 h-3 rounded-full"
                        :class="deviceConnected ? 'bg-emerald-400 animate-pulse' : 'bg-red-400'"></div>
                </div>
                <p class="text-lg font-bold" :class="deviceConnected ? 'text-emerald-400' : 'text-red-400'"
                    x-text="deviceConnected ? 'Connected' : 'Disconnected'"></p>
                <p class="text-xs text-slate-500 mt-1">Inforce Wireless 2D</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-slate-400 mb-2">Total Scan Hari Ini</p>
                <p class="text-2xl font-bold text-cyan-400">{{ $todayStats['total_scans'] }}</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-emerald-400 mb-2">Success</p>
                <p class="text-2xl font-bold text-emerald-400">{{ $todayStats['success'] }}</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <p class="text-xs text-red-400 mb-2">Failed</p>
                <p class="text-2xl font-bold text-red-400">{{ $todayStats['failed'] }}</p>
            </div>
        </div>

        <!-- Scan Form -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    Scan Barcode
                </h3>
            </div>

            <!-- Scan Type Selector -->
            <div class="grid grid-cols-3 md:grid-cols-5 gap-3 mb-6">
                <template x-for="type in scanTypes" :key="type.value">
                    <button @click="scanType = type.value"
                        class="px-4 py-3 rounded-xl border-2 transition-all text-sm font-semibold"
                        :class="scanType === type.value ?
                            'border-cyan-500 bg-cyan-500/10 text-cyan-400' :
                            'border-slate-700 bg-slate-800/50 text-slate-400 hover:border-slate-600'">
                        <span x-text="type.label"></span>
                    </button>
                </template>
            </div>

            <!-- Scan Form -->
            <form @submit.prevent="addToBuffer" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Scan Barcode <span class="text-red-400">*</span>
                        </label>
                        <input type="text" x-model="barcode" @keydown.enter.prevent="addToBuffer"
                            x-ref="barcodeInput" placeholder="Scan barcode di sini atau ketik manual..."
                            class="w-full px-4 py-4 bg-slate-800 border-2 border-slate-700 rounded-xl text-white text-lg font-mono placeholder-slate-500 focus:border-cyan-400 focus:outline-none transition-all"
                            autofocus>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Quantity *</label>
                        <input type="number" x-model="qty" min="1"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Lokasi Bin</label>
                        <input type="text" x-model="binCode" placeholder="RAK-A-01"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white font-mono placeholder-slate-500 focus:border-cyan-400 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">No. Referensi</label>
                        <input type="text" x-model="referenceNo" placeholder="PO-20261005-001"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white font-mono placeholder-slate-500 focus:border-cyan-400 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Catatan</label>
                        <input type="text" x-model="notes" placeholder="Opsional..."
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:outline-none transition-all">
                    </div>
                </div>

                <button type="submit" :disabled="processing || !barcode"
                    class="w-full py-4 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 disabled:from-slate-700 disabled:to-slate-700 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-lg shadow-cyan-500/25 transition-all flex items-center justify-center gap-2">
                    <svg x-show="!processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <svg x-show="processing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="processing ? 'Memproses...' : 'Tambah ke Buffer'"></span>
                </button>
            </form>

            <!-- Last Scan Result -->
            <div x-show="lastScan" x-transition class="mt-4 p-4 rounded-xl" :class="lastScanStatus">
                <p class="font-semibold" x-text="lastScan?.message"></p>
            </div>
        </div>

        <!-- Buffer Queue -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Buffer Queue (<span x-text="bufferItems.length"></span>)
                </h3>
                <div class="flex gap-2">
                    <button @click="confirmAll" :disabled="bufferItems.length === 0 || confirming"
                        class="px-4 py-2 border-2 border-slate-700 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 disabled:from-slate-700 disabled:to-slate-700 text-white font-semibold rounded-xl transition-all flex items-center gap-2">
                        <svg x-show="!confirming" class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <svg x-show="confirming" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="confirming ? 'Memproses...' : 'Confirm All'"></span>
                    </button>
                    <button @click="clearBuffer" :disabled="bufferItems.length === 0"
                        class="px-4 py-2 bg-red-600 hover:bg-red-500 disabled:bg-slate-700 text-white font-semibold rounded-xl transition-all">
                        Clear All
                    </button>
                </div>
            </div>

            <div class="space-y-2">
                <template x-if="bufferItems.length === 0">
                    <div class="text-center py-8 text-slate-500">
                        <p class="text-sm">Buffer kosong. Scan barcode untuk menambahkan.</p>
                    </div>
                </template>

                <template x-for="(item, index) in bufferItems" :key="item.id">
                    <div class="p-4 bg-slate-800/50 rounded-xl border border-slate-700/50"
                        :class="item.status === 'INVALID' ? 'border-red-500/50' : ''">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-mono font-bold text-white" x-text="item.barcode"></span>
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold"
                                        :class="getScanTypeColor(item.scan_type)" x-text="item.scan_type"></span>
                                    <span x-show="item.status === 'INVALID'"
                                        class="px-2 py-0.5 rounded text-xs font-semibold bg-red-500/20 text-red-400">INVALID</span>
                                </div>
                                <p class="text-xs text-slate-400" x-text="item.product_name + ' (' + item.size + ')'">
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button @click="editItem(item)"
                                    class="p-2 text-slate-400 hover:text-cyan-400 hover:bg-slate-700 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button @click="deleteItem(item.id)"
                                    class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-4 gap-2 text-xs">
                            <div>
                                <span class="text-slate-500">Qty:</span>
                                <span class="text-white font-semibold ml-1" x-text="item.qty"></span>
                            </div>
                            <div>
                                <span class="text-slate-500">Bin:</span>
                                <span class="text-white font-mono ml-1" x-text="item.bin_code || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500">Ref:</span>
                                <span class="text-white font-mono ml-1" x-text="item.reference_no || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500">Time:</span>
                                <span class="text-slate-400 ml-1" x-text="formatTime(item.created_at)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="editingItem" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-md w-full"
                @click.away="editingItem = null">
                <h4 class="text-lg font-semibold text-white mb-4">Edit Buffer Item</h4>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">SKU</label>
                        <input type="text" :value="editingItem?.barcode" disabled
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Quantity</label>
                        <input type="number" x-model="editForm.qty" min="1"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Bin Code</label>
                        <input type="text" x-model="editForm.bin_code"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Reference No</label>
                        <input type="text" x-model="editForm.reference_no"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Notes</label>
                        <textarea x-model="editForm.notes" rows="2"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-cyan-400 focus:outline-none"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button @click="saveEdit"
                        class="flex-1 py-3 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white font-semibold rounded-xl">Save</button>
                    <button @click="editingItem = null"
                        class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-xl border border-slate-700">Cancel</button>
                </div>
            </div>
        </div>

        <!-- Recent Scans History -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Riwayat Scan
            </h3>

            <div class="space-y-2">
                @forelse($recentScans as $scan)
                    <div
                        class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg border border-slate-700/50">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center {{ $scan->scan_type_color }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-mono font-semibold text-white">{{ $scan->sku }}</p>
                                <p class="text-xs text-slate-400">{{ $scan->masterSku?->product_name }} •
                                    {{ $scan->scan_type }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <p class="text-sm font-bold text-cyan-400">Qty: {{ $scan->qty }}</p>
                                <p class="text-xs text-slate-500">{{ $scan->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-1">
                                <button onclick="editScan('{{ $scan->id }}')"
                                    class="p-2 text-slate-400 hover:text-cyan-400 hover:bg-slate-700 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onclick="deleteScan('{{ $scan->id }}')"
                                    class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500">
                        <p class="text-sm">Belum ada riwayat scan</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function scannerApp() {
            return {
                barcode: '',
                scanType: 'INBOUND',
                qty: 1,
                binCode: '',
                referenceNo: '',
                notes: '',
                processing: false,
                confirming: false,
                deviceConnected: false,
                lastScan: null,
                lastScanStatus: '',
                bufferItems: [],
                editingItem: null,
                editForm: {
                    qty: 1,
                    bin_code: '',
                    reference_no: '',
                    notes: ''
                },

                scanTypes: [{
                        value: 'INBOUND',
                        label: 'Inbound'
                    },
                    {
                        value: 'OUTBOUND',
                        label: 'Outbound'
                    },
                    {
                        value: 'PICKING',
                        label: 'Picking'
                    },
                    {
                        value: 'PACKING',
                        label: 'Packing'
                    },
                    {
                        value: 'OPNAME',
                        label: 'Opname'
                    },
                ],

                init() {
                    this.$nextTick(() => {
                        this.$refs.barcodeInput.focus();
                    });

                    this.loadBuffer();
                    this.checkDevice();

                    // Check device setiap 5 detik
                    setInterval(() => this.checkDevice(), 5000);

                    // Listen keyboard untuk scanner HID
                    let barcodeBuffer = '';
                    let lastKeyTime = 0;

                    document.addEventListener('keydown', (e) => {
                        const now = Date.now();

                        // Jika interval antar key < 50ms, kemungkinan dari scanner
                        if (now - lastKeyTime < 50 && e.key.length === 1) {
                            barcodeBuffer += e.key;
                        } else {
                            barcodeBuffer = e.key.length === 1 ? e.key : '';
                        }

                        lastKeyTime = now;

                        if (e.key === 'Enter' && barcodeBuffer.length > 3) {
                            e.preventDefault();
                            this.barcode = barcodeBuffer;
                            this.addToBuffer();
                            barcodeBuffer = '';
                        }
                    });
                },

                async checkDevice() {
                    try {
                        const response = await fetch('{{ route('gudang.scanner.device') }}');
                        const data = await response.json();
                        this.deviceConnected = data.connected;
                    } catch (error) {
                        this.deviceConnected = false;
                    }
                },

                async addToBuffer() {
                    if (!this.barcode || this.processing) return;

                    this.processing = true;

                    try {
                        const response = await fetch('{{ route('gudang.scanner.buffer.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                barcode: this.barcode,
                                scan_type: this.scanType,
                                qty: this.qty,
                                bin_code: this.binCode,
                                reference_no: this.referenceNo,
                                notes: this.notes,
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.lastScan = {
                                message: data.message
                            };
                            this.lastScanStatus = data.item.status === 'INVALID' ?
                                'bg-red-900/30 border border-red-500/50 text-red-400' :
                                'bg-emerald-900/30 border border-emerald-500/50 text-emerald-400';

                            this.barcode = '';
                            this.qty = 1;
                            this.binCode = '';
                            this.notes = '';

                            this.loadBuffer();

                            this.$nextTick(() => {
                                this.$refs.barcodeInput.focus();
                            });
                        } else {
                            this.lastScan = {
                                message: data.message
                            };
                            this.lastScanStatus = 'bg-red-900/30 border border-red-500/50 text-red-400';
                        }
                    } catch (error) {
                        this.lastScan = {
                            message: 'Error: ' + error.message
                        };
                        this.lastScanStatus = 'bg-red-900/30 border border-red-500/50 text-red-400';
                    } finally {
                        this.processing = false;
                    }
                },

                async loadBuffer() {
                    try {
                        const response = await fetch('{{ route('gudang.scanner.buffer.get') }}');
                        const data = await response.json();
                        this.bufferItems = data.items;
                    } catch (error) {
                        console.error('Load buffer error:', error);
                    }
                },

                editItem(item) {
                    this.editingItem = item;
                    this.editForm = {
                        qty: item.qty,
                        bin_code: item.bin_code || '',
                        reference_no: item.reference_no || '',
                        notes: item.notes || '',
                    };
                },

                async saveEdit() {
                    try {
                        const url = '{{ route('gudang.scanner.buffer.update', ':id') }}'.replace(':id', this
                            .editingItem.id);

                        const response = await fetch(url, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.editForm)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.editingItem = null;
                            this.loadBuffer();
                        } else {
                            alert(data.message);
                        }
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                },

                async deleteItem(id) {
                    if (!confirm('Hapus item ini dari buffer?')) return;

                    try {
                        // ✅ BENAR: Gunakan placeholder :id lalu replace
                        const url = '{{ route('gudang.scanner.buffer.delete', ':id') }}'.replace(':id', id);

                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.loadBuffer();
                        } else {
                            alert(data.message);
                        }
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                },

                async clearBuffer() {
                    if (!confirm('Kosongkan semua buffer?')) return;

                    try {
                        const response = await fetch('{{ route('gudang.scanner.buffer.clear') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.bufferItems = [];
                        }
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                },

                async confirmAll() {
                    if (this.bufferItems.length === 0) return;

                    if (!confirm(`Proses ${this.bufferItems.length} item dari buffer?`)) return;

                    this.confirming = true;

                    try {
                        const response = await fetch('{{ route('gudang.scanner.buffer.confirm') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert(data.message);
                            this.bufferItems = [];
                            window.location.reload();
                        } else {
                            alert(data.message);
                        }
                    } catch (error) {
                        alert('Error: ' + error.message);
                    } finally {
                        this.confirming = false;
                    }
                },

                getScanTypeColor(type) {
                    const colors = {
                        'INBOUND': 'bg-blue-500/20 text-blue-400',
                        'OUTBOUND': 'bg-purple-500/20 text-purple-400',
                        'PICKING': 'bg-amber-500/20 text-amber-400',
                        'PACKING': 'bg-cyan-500/20 text-cyan-400',
                        'OPNAME': 'bg-pink-500/20 text-pink-400',
                        'TRANSFER': 'bg-indigo-500/20 text-indigo-400',
                        'QC_CHECK': 'bg-emerald-500/20 text-emerald-400',
                        'RETURN_IN': 'bg-orange-500/20 text-orange-400',
                        'RETURN_OUT': 'bg-red-500/20 text-red-400',
                    };
                    return colors[type] || 'bg-slate-500/20 text-slate-400';
                },

                formatTime(datetime) {
                    if (!datetime) return '-';
                    const date = new Date(datetime);
                    return date.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }
        }

        // Global functions untuk edit/delete scan history
        async function editScan(id) {
            const qty = prompt('Quantity:', '');
            const binCode = prompt('Bin Code:', '');

            if (qty === null) return;

            try {
                const response = await fetch(`/gudang/scanner/scans/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        qty: parseInt(qty),
                        bin_code: binCode
                    })
                });

                const data = await response.json();
                alert(data.message);

                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function deleteScan(id) {
            if (!confirm('Hapus scan record ini?')) return;

            try {
                const response = await fetch(`/gudang/scanner/scans/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                alert(data.message);

                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    </script>
</x-app-layout>
