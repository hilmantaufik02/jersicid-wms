<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Balik posisi rak A-D (dari POV depan ke belakang)
        // Sebelumnya: A=0, B=1, C=2, D=3 (kiri ke kanan)
        // Seharusnya: D=0, C=1, B=2, A=3 (A di kanan)

        DB::table('warehouse_racks')->where('code', 'A')->update(['position_x' => 3]);
        DB::table('warehouse_racks')->where('code', 'B')->update(['position_x' => 2]);
        DB::table('warehouse_racks')->where('code', 'C')->update(['position_x' => 1]);
        DB::table('warehouse_racks')->where('code', 'D')->update(['position_x' => 0]);
    }

    public function down(): void
    {
        // Rollback ke posisi semula
        DB::table('warehouse_racks')->where('code', 'A')->update(['position_x' => 0]);
        DB::table('warehouse_racks')->where('code', 'B')->update(['position_x' => 1]);
        DB::table('warehouse_racks')->where('code', 'C')->update(['position_x' => 2]);
        DB::table('warehouse_racks')->where('code', 'D')->update(['position_x' => 3]);
    }
};
