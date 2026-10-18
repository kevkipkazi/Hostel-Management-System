<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@hostelms.test'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
                'role' => 'administrator',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@hostelms.test'],
            [
                'name' => 'Hostel Manager',
                'password' => Hash::make('password123'),
                'role' => 'hostel_manager',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'finance@hostelms.test'],
            [
                'name' => 'Finance Officer',
                'password' => Hash::make('password123'),
                'role' => 'finance_officer',
                'is_active' => true,
            ]
        );
    }
}
