<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // Create main warehouse if not exists
        Warehouse::firstOrCreate(
            ['code' => 'GUDANG'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Gudang Utama',
                'code' => 'GUDANG',
                'status' => 'AKTIF',
            ]
        );

        Warehouse::firstOrCreate(
            ['code' => 'UTAMA'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Workspace Utama',
                'code' => 'UTAMA',
                'status' => 'AKTIF',
            ]
        );

        Warehouse::firstOrCreate(
            ['code' => 'PACKING'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Area Packing',
                'code' => 'PACKING',
                'status' => 'AKTIF',
            ]
        );

        Warehouse::firstOrCreate(
            ['code' => 'PO'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Purchase Order Workspace',
                'code' => 'PO',
                'status' => 'AKTIF',
            ]
        );

        $this->command->info('✅ Warehouse seeders completed!');
    }
}
