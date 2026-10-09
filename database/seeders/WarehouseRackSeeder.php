<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WarehouseRackSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data lama
        if (Schema::hasTable('rack_products')) {
            DB::table('rack_products')->truncate();
        }
        if (Schema::hasTable('warehouse_racks')) {
            DB::table('warehouse_racks')->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ✅ PERBAIKAN: Gunakan kode warna 3 huruf (RED, BLU, GRN, YLW, ORG, PRP, PNK, GRY, WHT, BLK, CYA, TEL, LIM, GLD, NVY)
        $racks = [
            // Dinding Belakang (Row 0) - Aisle 1
            ['code' => 'A', 'aisle_number' => 1, 'color' => 'GLD', 'rows_count' => 4, 'position_x' => 4, 'position_y' => 0],
            ['code' => 'B', 'aisle_number' => 1, 'color' => 'BLU', 'rows_count' => 4, 'position_x' => 3, 'position_y' => 0],
            ['code' => 'C', 'aisle_number' => 1, 'color' => 'GRN', 'rows_count' => 4, 'position_x' => 2, 'position_y' => 0],
            ['code' => 'D', 'aisle_number' => 1, 'color' => 'RED', 'rows_count' => 4, 'position_x' => 1, 'position_y' => 0],

            // Row 1 - Aisle 2
            ['code' => 'E',  'aisle_number' => 2, 'color' => 'PRP', 'rows_count' => 4, 'position_x' => 1, 'position_y' => 2, 'is_top_row' => true],
            ['code' => 'I',  'aisle_number' => 2, 'color' => 'CYA', 'rows_count' => 4, 'position_x' => 2, 'position_y' => 2],
            ['code' => 'M',  'aisle_number' => 2, 'color' => 'PNK', 'rows_count' => 4, 'position_x' => 3, 'position_y' => 2],
            ['code' => 'Q',  'aisle_number' => 2, 'color' => 'GLD', 'rows_count' => 4, 'position_x' => 4, 'position_y' => 2],
            ['code' => 'U',  'aisle_number' => 2, 'color' => 'LIM', 'rows_count' => 4, 'position_x' => 5, 'position_y' => 2],
            ['code' => 'Y',  'aisle_number' => 2, 'color' => 'TEL', 'rows_count' => 4, 'position_x' => 6, 'position_y' => 2, 'is_top_row' => true],

            // Row 2 - Aisle 3
            ['code' => 'F',  'aisle_number' => 3, 'color' => 'PRP', 'rows_count' => 4, 'position_x' => 1, 'position_y' => 3, 'is_top_row' => true],
            ['code' => 'J',  'aisle_number' => 3, 'color' => 'CYA', 'rows_count' => 4, 'position_x' => 2, 'position_y' => 3],
            ['code' => 'N',  'aisle_number' => 3, 'color' => 'PNK', 'rows_count' => 4, 'position_x' => 3, 'position_y' => 3],
            ['code' => 'R',  'aisle_number' => 3, 'color' => 'GLD', 'rows_count' => 4, 'position_x' => 4, 'position_y' => 3],
            ['code' => 'V',  'aisle_number' => 3, 'color' => 'LIM', 'rows_count' => 4, 'position_x' => 5, 'position_y' => 3],
            ['code' => 'Z',  'aisle_number' => 3, 'color' => 'TEL', 'rows_count' => 4, 'position_x' => 6, 'position_y' => 3, 'is_top_row' => true],

            // Row 3 - Aisle 4
            ['code' => 'G',  'aisle_number' => 4, 'color' => 'PRP', 'rows_count' => 4, 'position_x' => 1, 'position_y' => 4, 'is_top_row' => true],
            ['code' => 'K',  'aisle_number' => 4, 'color' => 'CYA', 'rows_count' => 4, 'position_x' => 2, 'position_y' => 4],
            ['code' => 'O',  'aisle_number' => 4, 'color' => 'PNK', 'rows_count' => 4, 'position_x' => 3, 'position_y' => 4],
            ['code' => 'S',  'aisle_number' => 4, 'color' => 'GLD', 'rows_count' => 4, 'position_x' => 4, 'position_y' => 4],
            ['code' => 'W',  'aisle_number' => 4, 'color' => 'LIM', 'rows_count' => 4, 'position_x' => 5, 'position_y' => 4],
            ['code' => 'AA', 'aisle_number' => 4, 'color' => 'TEL', 'rows_count' => 4, 'position_x' => 6, 'position_y' => 4, 'is_top_row' => true],

            // Row 4 - Aisle 5
            ['code' => 'H',  'aisle_number' => 5, 'color' => 'PRP', 'rows_count' => 4, 'position_x' => 1, 'position_y' => 5, 'is_top_row' => true],
            ['code' => 'L',  'aisle_number' => 5, 'color' => 'CYA', 'rows_count' => 4, 'position_x' => 2, 'position_y' => 5],
            ['code' => 'P',  'aisle_number' => 5, 'color' => 'PNK', 'rows_count' => 4, 'position_x' => 3, 'position_y' => 5],
            ['code' => 'T',  'aisle_number' => 5, 'color' => 'GLD', 'rows_count' => 4, 'position_x' => 4, 'position_y' => 5],
            ['code' => 'X',  'aisle_number' => 5, 'color' => 'LIM', 'rows_count' => 4, 'position_x' => 5, 'position_y' => 5],
            ['code' => 'AB', 'aisle_number' => 5, 'color' => 'TEL', 'rows_count' => 4, 'position_x' => 6, 'position_y' => 5, 'is_top_row' => true],
        ];

        foreach ($racks as $rack) {
            DB::table('warehouse_racks')->insert(array_merge($rack, [
                'id' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        echo "✅ Seeder berhasil! " . count($racks) . " rak telah dibuat.\n";
    }
}
