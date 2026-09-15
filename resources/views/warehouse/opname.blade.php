@extends('layouts.app')
@section('page-title', 'Stock Opname (Blind Count)')
@section('page-subtitle', 'Masukkan jumlah fisik. Sistem akan menghitung selisih secara real-time.')

@section('content')
<div x-data="opnameForm()" class="max-w-6xl mx-auto">
    <form @submit.prevent="submitOpname" method="POST" action="{{ route('gudang.opname.reconcile') }}">
        @csrf
        
        <div class="bg-wms-card border border-wms-border rounded-2xl p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-wms-accent">Lembar Hitung Fisik</h2>
                <button type="submit" :disabled="isSubmitting" class="bg-wms-accent hover:bg-wms-accent-hover text-slate-900 font-bold px-6 py-2 rounded-lg shadow-lg disabled:opacity-50">
                    <span x-text="isSubmitting ? 'Memproses...' : 'SIMPAN & REKONSILIASI'"></span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase text-wms-muted border-b border-wms-border">
                        <tr>
                            <th class="px-4 py-3">Lokasi Bin</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Nama Barang</th>
                            <th class="px-4 py-3 text-center">Stok Sistem</th>
                            <th class="px-4 py-3 text-center">Hitung Fisik</th>
                            <th class="px-4 py-3 text-center">Variance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $index => $stock)
                        <tr class="border-b border-wms-border/50 hover:bg-wms-bg/50">
                            <td class="px-4 py-3 font-mono text-wms-accent">{{ $stock->bin_code }}</td>
                            <td class="px-4 py-3 font-mono">{{ $stock->sku }}</td>
                            <td class="px-4 py-3 text-wms-text">{{ $stock->masterSku->product_name }}</td>
                            <td class="px-4 py-3 text-center font-bold text-white">{{ $stock->qty }}</td>
                            <td class="px-4 py-3 text-center">
                                <input type="number" name="counts[{{ $index }}][physical_qty]" 
                                       x-model.number="physicalCounts['{{ $stock->sku }}_{{ $stock->bin_code }}']"
                                       @input="calculateVariance('{{ $stock->sku }}_{{ $stock->bin_code }}', {{ $stock->qty }})"
                                       min="0" class="w-24 bg-wms-bg border border-wms-border rounded px-2 py-1 text-center text-white focus:border-wms-accent">
                                <input type="hidden" name="counts[{{ $index }}][sku]" value="{{ $stock->sku }}">
                                <input type="hidden" name="counts[{{ $index }}][bin_code]" value="{{ $stock->bin_code }}">
                            </td>
                            <td class="px-4 py-3 text-center font-bold">
                                <span x-text="variances['{{ $stock->sku }}_{{ $stock->bin_code }}'] || 0" 
                                      :class="{
                                          'text-emerald-400': (variances['{{ $stock->sku }}_{{ $stock->bin_code }}'] || 0) > 0,
                                          'text-red-400': (variances['{{ $stock->sku }}_{{ $stock->bin_code }}'] || 0) < 0,
                                          'text-wms-muted': (variances['{{ $stock->sku }}_{{ $stock->bin_code }}'] || 0) === 0
                                      }"></span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
    function opnameForm() {
        return {
            physicalCounts: {},
            variances: {},
            isSubmitting: false,

            calculateVariance(key, systemQty) {
                const physical = this.physicalCounts[key] || 0;
                this.variances[key] = physical - systemQty;
            },

            async submitOpname(e) {
                if (!confirm('Yakin ingin menyimpan hasil opname dan menyesuaikan stok?')) {
                    e.preventDefault();
                    return;
                }
                this.isSubmitting = true;
                e.target.submit();
            }
        }
    }
</script>
@endsection