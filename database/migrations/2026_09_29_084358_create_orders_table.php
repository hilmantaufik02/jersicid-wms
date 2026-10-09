<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('courier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->enum('status', [
                'PENDING',
                'CONFIRMED',
                'PICKING',
                'PACKING',
                'READY_TO_SHIP',
                'SHIPPED',
                'COMPLETED',
                'CANCELLED'
            ])->default('PENDING');

            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('picked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('packed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('picked_at')->nullable();
            $table->timestamp('packed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'order_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
