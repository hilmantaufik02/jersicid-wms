<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus jika sudah ada untuk memastikan clean state
        Schema::dropIfExists('warehouse_racks');

        Schema::create('warehouse_racks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 10); // Perpanjang sedikit untuk aman
            $table->integer('aisle_number')->nullable();
            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->integer('rows_count')->default(4);
            $table->boolean('is_top_row')->default(false);
            $table->string('color', 20)->default('GRAY'); // Perpanjang jadi 20 karakter

            $table->timestamps();

            // Tambahkan unique index
            $table->unique('code', 'warehouse_racks_code_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_racks');
    }
};
