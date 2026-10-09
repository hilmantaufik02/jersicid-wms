<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('supplier_code')->unique(); // SUP-001, SUP-002, dst
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('npwp')->nullable(); // Nomor Pokok Wajib Pajak
            $table->enum('type', ['SUBLIMASI', 'KONVEKSI', 'UMUM'])->default('UMUM');
            $table->enum('status', ['AKTIF', 'NONAKTIF'])->default('AKTIF');
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index('supplier_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};