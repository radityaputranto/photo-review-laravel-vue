<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@fotoapp.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'),
                'role'      => 'super_admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@fotoapp.com'],
            [
                'name'      => 'Admin Staff',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'photographer@fotoapp.com'],
            [
                'name'      => 'Fotografer',
                'password'  => Hash::make('password'),
                'role'      => 'photographer',
                'is_active' => true,
            ]
        );

        // Alias akun dengan domain @photoapp.com
        User::firstOrCreate(
            ['email' => 'superadmin@photoapp.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'),
                'role'      => 'super_admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@photoapp.com'],
            [
                'name'      => 'Admin Staff',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'photographer@photoapp.com'],
            [
                'name'      => 'Fotografer',
                'password'  => Hash::make('password'),
                'role'      => 'photographer',
                'is_active' => true,
            ]
        );

        // Customer dummy untuk testing
        User::firstOrCreate(
            ['email' => 'andi@example.com'],
            [
                'name'      => 'Andi Santoso',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
                'is_active' => true,
            ]
        );
    }
}
