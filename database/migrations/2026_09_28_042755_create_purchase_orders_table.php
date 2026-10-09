<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('po_number')->unique();
            $table->string('supplier_name');
            $table->string('supplier_type')->default('UMUM'); // SUBLIMASI / KONVEKSI / UMUM
            $table->date('expected_date');
            $table->enum('status', [
                'DRAFT',           // Belum dikirim
                'PENDING',         // Menunggu approval
                'APPROVED',        // Disetujui Owner/Spv
                'IN_PRODUCTION',   // Sedang diproduksi (SUBLIM/KONVEKSI)
                'READY',           // Siap dikirim ke gudang
                'RECEIVED',        // Sudah diterima gudang
                'CANCELLED'        // Dibatalkan
            ])->default('DRAFT');
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('production_notes')->nullable(); // Catatan dari SUBLIM/KONVEKSI
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'supplier_type']);
            $table->index('po_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
