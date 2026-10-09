<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printer_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Nama template
            $table->enum('type', [
                'SKU_LABEL',        // Label SKU
                'SHIPPING_LABEL',   // Label pengiriman
                'BIN_LABEL',        // Label lokasi bin
                'PO_LABEL',         // Label PO
                'CUSTOM'            // Custom template
            ]);
            $table->text('zpl_code'); // ZPL code untuk Zebra printer
            $table->integer('width_mm')->default(100); // Lebar label (mm)
            $table->integer('height_mm')->default(50); // Tinggi label (mm)
            $table->integer('dpi')->default(203); // DPI printer
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printer_templates');
    }
};
