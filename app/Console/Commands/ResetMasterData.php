<?php

namespace App\Console\Commands;

use App\Models\MasterSku;
use App\Models\StockActive;
use App\Models\StockMovement;
use App\Models\StockOpnameItem;
use App\Models\StockOpname;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\AuditTrail;
use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetMasterData extends Command
{
    protected $signature = 'data:reset-master {--force : Skip konfirmasi} {--all : Reset semua data termasuk PO, Order, Supplier}';
    protected $description = 'Reset data Master SKU dan data terkait';

    public function handle()
    {
        $resetAll = $this->option('all');

        if (!$this->option('force')) {
            $this->warn('⚠️  PERINGATAN: Perintah ini akan menghapus data!');
            $this->newLine();

            if ($resetAll) {
                $this->line('   📦 Master SKU');
                $this->line('   📦 Stock Active');
                $this->line('   📦 Stock Movement');
                $this->line('   📦 Stock Opname & Items');
                $this->line('   📦 Purchase Orders & Items');
                $this->line('   📦 Orders & Order Items');
                $this->line('   📦 Suppliers');
                $this->line('    Audit Trails');
            } else {
                $this->line('   📦 Master SKU');
                $this->line('   📦 Stock Active');
                $this->line('   📦 Stock Movement');
                $this->line('    Stock Opname Items');
                $this->line('   📦 Purchase Order Items');
            }

            $this->newLine();

            if (!$this->confirm('Yakin ingin melanjutkan?', false)) {
                $this->info('❌ Dibatalkan.');
                return 0;
            }
        }

        $this->info('🗑️  Memulai reset data...');
        $this->newLine();

        try {
            // ✅ Nonaktifkan foreign key checks sementara
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            if ($resetAll) {
                $this->resetAllData();
            } else {
                $this->resetMasterDataOnly();
            }

            // ✅ Aktifkan kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->newLine();
            $this->info('✅ Reset berhasil!');
            $this->info('💡 Sekarang Anda bisa import ulang data via: http://127.0.0.1:8000/reports/import-sku');

            return 0;
        } catch (\Exception $e) {
            // ✅ Pastikan foreign key checks aktif kembali jika error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->error('❌ Gagal: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Reset hanya data Master SKU dan terkait
     */
    protected function resetMasterDataOnly(): void
    {
        $skuCount = MasterSku::count();
        $stockCount = StockActive::count();
        $movementCount = StockMovement::count();

        $this->line("📊 Data sebelum reset:");
        $this->table(
            ['Tabel', 'Jumlah'],
            [
                ['Master SKU', $skuCount],
                ['Stock Active', $stockCount],
                ['Stock Movement', $movementCount],
            ]
        );

        // Truncate dengan urutan yang aman
        $this->info('🔹 Menghapus Stock Movements...');
        StockMovement::truncate();

        $this->info('🔹 Menghapus Stock Actives...');
        StockActive::truncate();

        $this->info('🔹 Menghapus Stock Opname Items...');
        StockOpnameItem::truncate();

        $this->info('🔹 Menghapus Purchase Order Items...');
        PurchaseOrderItem::truncate();

        $this->info('🔹 Menghapus Master SKU...');
        MasterSku::truncate();

        $this->newLine();
        $this->info('✅ Data Master SKU berhasil direset!');
        $this->line("   Master SKU: 0");
        $this->line("   Stock Active: 0");
    }

    /**
     * Reset SEMUA data (nuclear option)
     */
    protected function resetAllData(): void
    {
        $this->warn('️  MODE RESET TOTAL - Semua data akan dihapus!');
        $this->newLine();

        $this->info('🔹 Menghapus Audit Trails...');
        AuditTrail::truncate();

        $this->info('🔹 Menghapus Stock Movements...');
        StockMovement::truncate();

        $this->info('🔹 Menghapus Stock Opname Items...');
        StockOpnameItem::truncate();

        $this->info('🔹 Menghapus Stock Opnames...');
        StockOpname::truncate();

        $this->info('🔹 Menghapus Order Items...');
        OrderItem::truncate();

        $this->info('🔹 Menghapus Orders...');
        Order::truncate();

        $this->info('🔹 Menghapus Purchase Order Items...');
        PurchaseOrderItem::truncate();

        $this->info('🔹 Menghapus Purchase Orders...');
        PurchaseOrder::truncate();

        $this->info('🔹 Menghapus Stock Actives...');
        StockActive::truncate();

        $this->info('🔹 Menghapus Master SKU...');
        MasterSku::truncate();

        $this->info('🔹 Menghapus Suppliers...');
        Supplier::truncate();

        $this->newLine();
        $this->info('✅ SEMUA data berhasil direset!');
    }
}
