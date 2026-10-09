<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Sesuai PRD v3.0 - jersic.id WMS
     * 
     * Catatan: Sistem TIDAK menggunakan tabel roles/permissions terpisah.
     * Hak akses ditentukan oleh ENUM 'role' di tabel users + middleware.
     */
    public function run(): void
    {
        $this->call([
            OwnerUserSeeder::class,
            WarehouseSeeder::class,
            WarehouseRackSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
