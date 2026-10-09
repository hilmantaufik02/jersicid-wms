<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('picking_lists', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->string('sku');
            $table->integer('qty');
            $table->string('bin_code');
            $table->enum('status', ['PENDING', 'PICKING', 'COMPLETED'])->default('PENDING');
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['order_number', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picking_lists');
    }
};
