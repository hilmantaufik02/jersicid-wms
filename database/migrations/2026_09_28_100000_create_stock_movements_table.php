<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sku');
            $table->string('bin_code');
            $table->enum('type', ['INBOUND', 'OUTBOUND', 'TRANSFER', 'ADJUSTMENT', 'RETURN']);
            $table->integer('qty');
            $table->integer('qty_before')->default(0);
            $table->integer('qty_after')->default(0);
            $table->string('reference_no')->nullable();
            $table->string('reference_type')->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['sku', 'type']);
            $table->index('reference_no');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
