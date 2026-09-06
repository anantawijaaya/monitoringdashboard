<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Role: USER (Bertindak sebagai ADMIN - Akses Penuh)
        User::updateOrCreate(
            ['email' => 'admin.balinusra@telkomsel.co.id'],
            [
                'name' => 'Ananta Wijaya',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@telkomsel.co.id'],
            [
                'name' => 'Admin Telkomsel',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        // 2. Role: VISITOR (Read-Only - Hanya Melihat Tampilan Dashboard)
        User::updateOrCreate(
            ['email' => 'visitor@telkomsel.co.id'],
            [
                'name' => 'Visitor Telkomsel',
                'password' => Hash::make('password'),
                'role' => 'visitor',
            ]
        );

        User::updateOrCreate(
            ['email' => 'visitor.balinusra@telkomsel.co.id'],
            [
                'name' => 'Visitor Bali Nusra',
                'password' => Hash::make('password'),
                'role' => 'visitor',
            ]
        );

        User::updateOrCreate(
            ['email' => 'visitor1@gmail.com'],
            [
                'name' => 'Visitor 1',
                'password' => Hash::make('visitor1'),
                'role' => 'visitor',
            ]
        );

        // 3. User Catalist SBP (Role: USER)
        User::updateOrCreate(
            ['email' => 'catalist@mitrasbp'],
            [
                'name' => 'Catalist SBP',
                'password' => Hash::make('balitengah'),
                'role' => 'user',
            ]
        );
    }
}
