<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // CREATE, UPDATE, DELETE, INBOUND, OUTBOUND
            $table->string('module');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            // Index untuk mempercepat filtering log
            $table->index(['action', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
