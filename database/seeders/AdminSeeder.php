<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'      => 'Super Admin',
            'email'     => 'superadmin@fotoapp.com',
            'password'  => Hash::make('password'),
            'role'      => 'super_admin',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Admin Staff',
            'email'     => 'admin@fotoapp.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Fotografer',
            'email'     => 'photographer@fotoapp.com',
            'password'  => Hash::make('password'),
            'role'      => 'photographer',
            'is_active' => true,
        ]);

        // Customer dummy untuk testing
        User::create([
            'name'      => 'Andi Santoso',
            'email'     => 'andi@example.com',
            'password'  => Hash::make('password'),
            'role'      => 'customer',
            'is_active' => true,
        ]);
    }
}
