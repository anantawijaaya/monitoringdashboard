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
        // -------------------------------------------------------------
        // 1. ROLE: ADMIN (2 Akun - Akses Penuh ke Seluruh 12 Cluster)
        // -------------------------------------------------------------
        User::updateOrCreate(
            ['email' => 'anantawijaya153@gmail.com'],
            [
                'name' => 'Ananta Wijaya',
                'password' => Hash::make('anantawijaya153.com!'),
                'role' => 'admin',
                'cluster_name' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'kevin.herywanto@gmail.com'],
            [
                'name' => 'Kevin Herywanto',
                'password' => Hash::make('kevin.herywanto@gmail.com'),
                'role' => 'admin',
                'cluster_name' => null,
            ]
        );
         User::updateOrCreate(
            ['email' => 'dicky86.dm@gmail.com'],
            [
                'name' => 'Dicky Mahendra',
                'password' => Hash::make('dicky86.dm@gmail.com'),
                'role' => 'admin',
                'cluster_name' => null,
            ]
        );
         User::updateOrCreate(
            ['email' => 'yuli.0939@gmail.com'],
            [
                'name' => 'Yuliastuti',
                'password' => Hash::make('yuli.0939@gmail.com'),
                'role' => 'admin',
                'cluster_name' => null,
            ]
        );

        // -------------------------------------------------------------
        // 2. ROLE: VISITOR (12 Akun Terkunci Spesifik per Cluster)
        // -------------------------------------------------------------
        $clusterAccounts = [
            ['email' => 'chanskyexo@gmail.com',            'name' => 'Admin Cluster Bali Barat',         'cluster' => 'BALI BARAT'],
            ['email' => 'ayudewi20005@gmail.com',          'name' => 'Admin Cluster Bali Tengah',        'cluster' => 'BALI TENGAH'],
            ['email' => 'rukirubiberdua@gmail.com',        'name' => 'Admin Cluster Bali Timur',         'cluster' => 'BALI TIMUR'],
            ['email' => 'lombok.balinusra@gmail.com',      'name' => 'Admin Cluster Lombok',             'cluster' => 'LOMBOK'],
            ['email' => 'sumbawa.balinusra@gmail.com',     'name' => 'Admin Cluster Sumbawa',            'cluster' => 'SUMBAWA'],
            ['email' => 'sumbawatimur.balinusra@gmail.com','name' => 'Admin Cluster Sumbawa Timur',      'cluster' => 'SUMBAWA TIMUR'],
            ['email' => 'kupangrote.balinusra@gmail.com',  'name' => 'Admin Cluster Kupang Rote',        'cluster' => 'KUPANG ROTE'],
            ['email' => 'malakabelu.balinusra@gmail.com',  'name' => 'Admin Cluster Malaka Timtim Belu', 'cluster' => 'MALAKA TIMTIM BELU'],
            ['email' => 'endesikka.balinusra@gmail.com',   'name' => 'Admin Cluster Ende Sikka',         'cluster' => 'ENDE SIKKA'],
            ['email' => 'florestimur.balinusra@gmail.com', 'name' => 'Admin Cluster Flores Timur',       'cluster' => 'FLORES TIMUR'],
            ['email' => 'manggarai.balinusra@gmail.com',   'name' => 'Admin Cluster Manggarai',          'cluster' => 'MANGGARAI'],
            ['email' => 'sumba.balinusra@gmail.com',       'name' => 'Admin Cluster Sumba',              'cluster' => 'SUMBA'],
        ];

        foreach ($clusterAccounts as $acc) {
            User::updateOrCreate(
                ['email' => $acc['email']],
                [
                    'name' => $acc['name'],
                    'password' => Hash::make('Visitor123!'),
                    'role' => 'visitor',
                    'cluster_name' => $acc['cluster'],
                ]
            );
        }
    }
}
