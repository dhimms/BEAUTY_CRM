<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@beautycrm.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );
        $admin->assignRole('Admin');

        // Sample Sales
        $sales = User::firstOrCreate(
            ['email' => 'sales@beautycrm.com'],
            [
                'name' => 'Sales Team',
                'password' => Hash::make('password'),
                'phone' => '081234567891',
                'is_active' => true,
            ]
        );
        $sales->assignRole('Sales');

        // Sample CS
        $cs = User::firstOrCreate(
            ['email' => 'cs@beautycrm.com'],
            [
                'name' => 'Customer Service',
                'password' => Hash::make('password'),
                'phone' => '081234567892',
                'is_active' => true,
            ]
        );
        $cs->assignRole('Customer Service');

        // Sample Manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@beautycrm.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password'),
                'phone' => '081234567893',
                'is_active' => true,
            ]
        );
        $manager->assignRole('Manager');
    }
}