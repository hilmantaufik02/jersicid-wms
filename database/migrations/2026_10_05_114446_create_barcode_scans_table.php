<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barcode_scans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('sku');
            $table->string('bin_code')->nullable();
            $table->enum('scan_type', [
                'INBOUND',      // Penerimaan barang
                'OUTBOUND',     // Pengiriman barang
                'PICKING',      // Proses picking
                'PACKING',      // Proses packing
                'OPNAME',       // Stock opname
                'TRANSFER',     // Transfer stok
                'QC_CHECK',     // Quality control
                'RETURN_IN',    // Retur masuk
                'RETURN_OUT'    // Retur keluar
            ]);
            $table->integer('qty')->default(1);
            $table->string('reference_no')->nullable(); // No. PO, No. Order, dll
            $table->enum('status', ['SUCCESS', 'FAILED', 'WARNING'])->default('SUCCESS');
            $table->text('message')->nullable(); // Pesan error/warning
            $table->text('notes')->nullable();
            $table->string('scanner_device')->nullable(); // Nama device scanner
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['sku', 'scan_type']);
            $table->index('user_id');
            $table->index('reference_no');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barcode_scans');
    }
};
