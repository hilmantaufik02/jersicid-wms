<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qc_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->string('batch_no');
            $table->foreignUuid('inspector_id')->constrained('users')->restrictOnDelete();
            $table->enum('result', ['PASS', 'FAIL', 'REWORK'])->default('PASS');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['sku', 'batch_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qc_logs');
    }
};
