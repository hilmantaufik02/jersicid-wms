<?php

namespace App\Services;

use App\Models\StockActive;
use App\Models\MasterSku;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryMovementService
{
    /**
     * PROSES INBOUND (Penerimaan Barang)
     * Menjamin stok bertambah secara atomik dan aman dari race condition.
     */
    public function processInbound(string $sku, string $binCode, int $qty, ?string $batchNo = null): StockActive
    {
        return DB::transaction(function () use ($sku, $binCode, $qty, $batchNo) {
            
            // 1. Validasi SKU existence & status
            $masterSku = MasterSku::where('sku', $sku)->where('status', 'AKTIF')->first();
            if (!$masterSku) {
                throw new Exception("SKU {$sku} tidak ditemukan atau tidak aktif.");
            }

            // 2. Lock baris untuk mencegah race condition (Pessimistic Locking)
            $stock = StockActive::where('sku', $sku)
                                ->where('bin_code', $binCode)
                                ->lockForUpdate()
                                ->first();

            // 3. Atomic Update atau Create
            if ($stock) {
                // Gunakan increment atomik MySQL (lebih aman daripada $stock->qty += $qty)
                $stock->increment('qty', $qty);
                $stock->update([
                    'batch_no'         => $batchNo ?? $stock->batch_no,
                    'last_movement_at' => now()
                ]);
            } else {
                $stock = StockActive::create([
                    'sku'              => $sku,
                    'bin_code'         => $binCode,
                    'qty'              => $qty,
                    'reserved_qty'     => 0,
                    'batch_no'         => $batchNo,
                    'received_at'      => now(),
                    'last_movement_at' => now(),
                ]);
            }

            return $stock->fresh();
        });
    }

    /**
     * PROSES OUTBOUND (Pengeluaran Barang)
     * Menjamin stok tidak pernah minus (overselling prevention).
     */
    public function processOutbound(string $sku, string $binCode, int $qty): StockActive
    {
        return DB::transaction(function () use ($sku, $binCode, $qty) {
            
            // 1. Lock baris
            $stock = StockActive::where('sku', $sku)
                                ->where('bin_code', $binCode)
                                ->lockForUpdate()
                                ->first();

            if (!$stock) {
                throw new Exception("Stok untuk SKU {$sku} di bin {$binCode} tidak ditemukan.");
            }

            // 2. Validasi ketersediaan stok (Available = Total - Reserved)
            $availableQty = $stock->qty - $stock->reserved_qty;

            if ($availableQty < $qty) {
                throw new Exception("Stok tidak mencukupi. Tersedia: {$availableQty}, Diminta: {$qty}.");
            }

            // 3. Atomic Decrement
            $stock->decrement('qty', $qty);
            $stock->update(['last_movement_at' => now()]);

            return $stock->fresh();
        });
    }

    /**
     * PROSES RESERVASI STOK (Untuk Outbound Picking/Packing)
     * Mengurangi available_qty tanpa mengurangi physical qty.
     */
    public function reserveStock(string $sku, string $binCode, int $qty): StockActive
    {
        return DB::transaction(function () use ($sku, $binCode, $qty) {
            $stock = StockActive::where('sku', $sku)
                                ->where('bin_code', $binCode)
                                ->lockForUpdate()
                                ->firstOrFail();

            if ($stock->available_qty < $qty) {
                throw new Exception("Stok tersedia tidak cukup untuk di-reserve.");
            }

            $stock->increment('reserved_qty', $qty);
            
            return $stock->fresh();
        });
    }

    /**
     * PROSES ADJUSTMENT (Stock Opname Variance)
     * Menyesuaikan stok fisik dengan sistem. Positif = Inbound, Negatif = Outbound.
     */
    public function processAdjustment(string $sku, string $binCode, int $variance): StockActive
    {
        if ($variance === 0) {
            throw new Exception("Variance adalah 0. Tidak ada penyesuaian yang diperlukan.");
        }

        return DB::transaction(function () use ($sku, $binCode, $variance) {
            $stock = StockActive::where('sku', $sku)
                                ->where('bin_code', $binCode)
                                ->lockForUpdate()
                                ->first();

            if (!$stock) {
                if ($variance > 0) {
                    // Jika stok tidak ada di sistem tapi ada di fisik (variance positif), buat baru
                    $stock = StockActive::create([
                        'sku' => $sku, 'bin_code' => $binCode, 'qty' => $variance,
                        'reserved_qty' => 0, 'received_at' => now(), 'last_movement_at' => now()
                    ]);
                } else {
                    throw new Exception("Stok tidak ditemukan di sistem untuk dikurangi.");
                }
            } else {
                $newQty = $stock->qty + $variance;
                if ($newQty < 0) {
                    throw new Exception("Penyesuaian akan membuat stok menjadi negatif. Periksa kembali fisik.");
                }
                
                $stock->update(['qty' => $newQty, 'last_movement_at' => now()]);
            }

            // Catat ke Audit Trail secara eksplisit sebagai ADJUSTMENT
            AuditTrail::create([
                'user_id'    => Auth::id(),
                'action'     => 'ADJUSTMENT',
                'module'     => 'STOCK_OPNAME',
                'old_values' => ['sku' => $sku, 'bin_code' => $binCode, 'variance' => $variance],
                'new_values' => ['qty' => $stock->qty],
                'ip_address' => request()->ip(),
            ]);

            return $stock->fresh();
        });
    }
}