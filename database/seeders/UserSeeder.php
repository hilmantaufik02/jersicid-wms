<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        $cashierRole = Role::where('slug', 'cashier')->first();
        $warehouseManagerRole = Role::where('slug', 'warehouse_manager')->first();

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@jersic.id',
            'password' => Hash::make('password'),
            'role_id' => $superAdminRole->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Kasir Demo',
            'email' => 'kasir@jersic.id',
            'password' => Hash::make('password'),
            'role_id' => $cashierRole->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Warehouse Manager',
            'email' => 'manager@jersic.id',
            'password' => Hash::make('password'),
            'role_id' => $warehouseManagerRole->id,
            'is_active' => true,
        ]);
    }
}
