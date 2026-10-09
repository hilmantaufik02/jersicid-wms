<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('sku');
            $table->string('bin_code')->nullable();
            $table->integer('qty_ordered');
            $table->integer('qty_picked')->default(0);
            $table->integer('qty_packed')->default(0);
            $table->unsignedBigInteger('price')->default(0);
            $table->timestamps();

            $table->foreign('sku')->references('sku')->on('master_skus')->cascadeOnDelete();
            $table->index(['sku', 'bin_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
