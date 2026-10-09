<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel jika sudah ada
        if (Schema::hasTable('rack_products')) {
            Schema::dropIfExists('rack_products');
        }

        Schema::create('rack_products', function (Blueprint $table) {
            $table->id();

            // ✅ FIX: Gunakan uuid() karena warehouse_racks.id adalah char(36)
            $table->uuid('warehouse_rack_id');
            $table->foreign('warehouse_rack_id')
                ->references('id')
                ->on('warehouse_racks')
                ->cascadeOnDelete();

            $table->string('sku');
            $table->foreign('sku')
                ->references('sku')
                ->on('master_skus')
                ->cascadeOnDelete();

            $table->integer('current_qty')->default(0);
            $table->integer('max_capacity')->default(100);
            $table->string('bin_location')->nullable();
            $table->timestamps();

            // Unique constraint
            $table->unique(['warehouse_rack_id', 'sku'], 'rack_products_rack_sku_unique');

            // Index untuk performa
            $table->index('warehouse_rack_id', 'idx_rack_products_rack_id');
            $table->index('sku', 'idx_rack_products_sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rack_products');
    }
};
