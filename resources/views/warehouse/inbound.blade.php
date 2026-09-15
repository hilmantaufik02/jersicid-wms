@extends('layouts.app')

@section('title', 'Inbound Receiving')
@section('page-title', 'Inbound Receiving')
@section('page-subtitle', 'Scan barcode SKU dan tentukan lokasi Bin untuk penerimaan barang.')

@section('content')
<div x-data="warehouseScanner('{{ route('gudang.inbound.store') }}', 'INBOUND')" class="max-w-4xl mx-auto">
    
    <!-- Status Feedback Banner -->
    <div x-show="statusMessage" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         :class="isSuccess ? 'bg-emerald-900/30 border-emerald-500/50 text-emerald-400' : 'bg-red-900/30 border-red-500/50 text-red-400'"
         class="mb-6 p-4 rounded-lg border flex items-center gap-3 shadow-lg">
        <svg x-show="isSuccess" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg x-show="!isSuccess" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        <span class="font-medium" x-text="statusMessage"></span>
    </div>

    <!-- Scanner Container -->
    <div class="bg-wms-card border border-wms-border rounded-2xl shadow-2xl shadow-cyan-900/10 p-6 md:p-8">
        <form @submit.prevent="submitData" class="space-y-6">
            
            <!-- SKU Input (Auto-focus untuk Scanner) -->
            <div>
                <label for="sku" class="block text-sm font-medium text-wms-accent mb-2 uppercase tracking-wider">SKU / Barcode</label>
                <input type="text" id="sku" x-ref="skuInput" x-model="form.sku" 
                       @keydown.enter.prevent="$refs.bin.focus()"
                       class="w-full bg-wms-bg border-2 border-wms-border focus:border-wms-accent rounded-lg px-4 py-3 text-xl text-white font-mono tracking-widest placeholder-slate-500 transition-colors focus:shadow-neon"
                       placeholder="Scan barcode di sini..." autofocus required>
            </div>

            <!-- Bin Code Input -->
            <div>
                <label for="bin" class="block text-sm font-medium text-wms-accent mb-2 uppercase tracking-wider">Lokasi Bin (RAK-BLOK-BARIS-LEVEL)</label>
                <input type="text" id="bin" x-ref="bin" x-model="form.bin_code" 
                       @keydown.enter.prevent="$refs.qty.focus()"
                       class="w-full bg-wms-bg border-2 border-wms-border focus:border-wms-accent rounded-lg px-4 py-3 text-xl text-white font-mono tracking-widest placeholder-slate-500 transition-colors focus:shadow-neon"
                       placeholder="Contoh: RAK-A-01-01" required>
            </div>

            <!-- Qty & Batch -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="qty" class="block text-sm font-medium text-wms-accent mb-2 uppercase tracking-wider">Jumlah (Qty)</label>
                    <input type="number" id="qty" x-ref="qty" x-model="form.qty" min="1"
                           @keydown.enter.prevent="submitData()"
                           class="w-full bg-wms-bg border-2 border-wms-border focus:border-wms-accent rounded-lg px-4 py-3 text-xl text-white font-mono placeholder-slate-500 transition-colors focus:shadow-neon"
                           placeholder="0" required>
                </div>
                <div>
                    <label for="batch" class="block text-sm font-medium text-wms-accent mb-2 uppercase tracking-wider">No. Batch (Opsional)</label>
                    <input type="text" id="batch" x-model="form.batch_no"
                           @keydown.enter.prevent="submitData()"
                           class="w-full bg-wms-bg border-2 border-wms-border focus:border-wms-accent rounded-lg px-4 py-3 text-xl text-white font-mono placeholder-slate-500 transition-colors focus:shadow-neon"
                           placeholder="Batch ID">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    :disabled="isProcessing"
                    class="w-full bg-wms-accent hover:bg-wms-accent-hover text-slate-900 font-bold py-4 rounded-lg shadow-lg shadow-cyan-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-lg">
                <svg x-show="isProcessing" class="animate-spin h-5 w-5 text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isProcessing ? 'Memproses...' : 'KONFIRMASI INBOUND (ENTER)'"></span>
            </button>
        </form>
    </div>

    <!-- Recent Activity Log (Real-time) -->
    <div class="mt-8 bg-wms-card border border-wms-border rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-wms-accent mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Riwayat Scan Terakhir (Sesi Ini)
        </h2>
        <div class="space-y-2">
            <template x-for="log in recentLogs" :key="log.id">
                <div class="flex justify-between items-center bg-wms-bg/50 p-3 rounded-lg border border-wms-border">
                    <div class="flex items-center gap-4">
                        <span class="text-white font-mono font-bold" x-text="log.sku"></span>
                        <span class="text-wms-muted text-sm" x-text="`→ ${log.bin_code}`"></span>
                    </div>
                    <span class="text-emerald-400 font-bold font-mono" x-text="`+${log.qty} PCS`"></span>
                </div>
            </template>
            <p x-show="recentLogs.length === 0" class="text-wms-muted text-center py-4 italic">Belum ada aktivitas scan pada sesi ini.</p>
        </div>
    </div>
</div>

<!-- Reusable Alpine.js Component Logic -->
<script>
    function warehouseScanner(endpoint, type) {
        return {
            endpoint: endpoint,
            type: type,
            form: {
                sku: '',
                bin_code: '',
                qty: 1,
                batch_no: ''
            },
            isProcessing: false,
            statusMessage: '',
            isSuccess: false,
            recentLogs: [],

            async submitData() {
                if (!this.form.sku || !this.form.bin_code || !this.form.qty) {
                    this.showStatus('Harap lengkapi SKU, Bin, dan Qty!', false);
                    return;
                }

                this.isProcessing = true;
                this.statusMessage = '';

                try {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const payload = { ...this.form };
                    
                    // Hapus batch_no jika kosong dan type bukan INBOUND
                    if (this.type !== 'INBOUND') delete payload.batch_no;

                    const response = await fetch(this.endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showStatus(result.message, true);
                        this.recentLogs.unshift({
                            id: Date.now(),
                            sku: this.form.sku,
                            bin_code: this.form.bin_code,
                            qty: this.form.qty
                        });
                        if (this.recentLogs.length > 5) this.recentLogs.pop();
                        
                        // Reset form untuk scan berikutnya, pertahankan bin_code untuk efisiensi
                        this.form.sku = '';
                        this.form.qty = 1;
                        this.form.batch_no = '';
                        this.$refs.skuInput.focus(); // Auto-focus kembali ke scanner
                    } else {
                        this.showStatus(result.message || 'Terjadi kesalahan validasi.', false);
                    }
                } catch (error) {
                    this.showStatus('Error koneksi ke server. Periksa jaringan Anda.', false);
                } finally {
                    this.isProcessing = false;
                }
            },

            showStatus(msg, success) {
                this.statusMessage = msg;
                this.isSuccess = success;
                setTimeout(() => { this.statusMessage = ''; }, 5000);
            }
        }
    }
</script>
@endsection