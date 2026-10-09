<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('session_number')->unique();
            $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignUuid('picking_session_id')->nullable()->constrained('picking_sessions')->nullOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['PENDING', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->string('tracking_number')->nullable();
            $table->string('courier')->nullable();
            $table->string('package_weight_gram')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing_sessions');
    }
};
