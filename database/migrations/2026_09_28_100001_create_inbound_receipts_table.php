<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('receipt_number')->unique();
            $table->foreignUuid('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->string('supplier_name')->nullable();
            $table->enum('source_type', ['PURCHASE_ORDER', 'RETURN', 'MANUAL'])->default('PURCHASE_ORDER');
            $table->date('receipt_date');
            $table->enum('status', ['DRAFT', 'PROCESSING', 'COMPLETED', 'CANCELLED'])->default('DRAFT');
            $table->foreignUuid('received_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['receipt_number', 'status']);
            $table->index('receipt_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_receipts');
    }
};
