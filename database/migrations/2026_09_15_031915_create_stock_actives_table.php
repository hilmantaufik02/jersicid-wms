<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_actives', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sku');
            $table->string('bin_code'); // Format RAK-BLOK-BARIS-LEVEL
            $table->integer('qty')->default(0);
            $table->integer('reserved_qty')->default(0);
            $table->string('batch_no')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('last_movement_at')->nullable();
            $table->timestamps();

            // Foreign Key & Indexing (Sesuai PRD 4.3)
            $table->foreign('sku')->references('sku')->on('master_skus')->onDelete('cascade');
            $table->index(['sku', 'bin_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_actives');
    }
};
