<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_skus', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique(); // Format: JER-[ARTIKEL]-[SIZE]
            $table->string('article');
            $table->string('product_name');
            $table->string('version');
            $table->string('sub_version')->nullable();
            $table->string('size_category'); // DEWASA/ KIDS/ TEENS
            $table->string('size');
            $table->string('size_token')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedInteger('standard_weight_gram')->default(250);
            $table->unsignedInteger('min_stock')->default(0);
            $table->string('status')->default('AKTIF');
            $table->timestamps();
            $table->softDeletes();

            // Indexing untuk performa query (Sesuai PRD 4.2)
            $table->index(['product_name', 'status']);
            $table->index('article');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_skus');
    }
};
