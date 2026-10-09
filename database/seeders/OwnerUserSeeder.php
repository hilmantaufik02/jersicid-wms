<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OwnerUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Admin Owner',
            'email' => 'owner@jersic.id',
            'password' => Hash::make('Owner@2026'),
            'role' => 'OWNER',
            'workspace_default' => 'UTAMA',
            'status' => 'AKTIF',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Akun OWNER berhasil dibuat!');
        $this->command->info('   Email: owner@jersic.id');
        $this->command->info('   Password: Owner@2026');
    }
}
