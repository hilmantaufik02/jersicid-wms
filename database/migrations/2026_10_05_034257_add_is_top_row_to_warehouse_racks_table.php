<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_racks', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse_racks', 'is_top_row')) {
                $table->boolean('is_top_row')->default(false)->after('rows_count');
            }
            if (!Schema::hasColumn('warehouse_racks', 'position_y')) {
                $table->integer('position_y')->default(0)->after('is_top_row');
            }
            if (!Schema::hasColumn('warehouse_racks', 'position_x')) {
                $table->integer('position_x')->default(0)->after('position_y');
            }
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_racks', function (Blueprint $table) {
            $table->dropColumn(['is_top_row', 'position_y', 'position_x']);
        });
    }
};
