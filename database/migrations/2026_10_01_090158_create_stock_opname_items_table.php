<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('stock_opname_id');
            $table->string('sku');
            $table->string('bin_code');
            $table->integer('system_qty');
            $table->integer('physical_qty')->nullable();
            $table->integer('difference')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['sku', 'bin_code']);
            $table->index('stock_opname_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
