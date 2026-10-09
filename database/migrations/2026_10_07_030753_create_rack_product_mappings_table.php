<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rack_product_mappings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rack_id')->constrained('warehouse_racks')->onDelete('cascade');
            $table->string('sku');
            $table->integer('qty')->default(0);
            $table->integer('max_capacity')->default(100);
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->foreign('sku')->references('sku')->on('master_skus')->onDelete('cascade');
            $table->unique(['rack_id', 'sku']);
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rack_product_mappings');
    }
};
