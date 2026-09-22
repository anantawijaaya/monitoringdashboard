<?php

namespace Database\Seeders;

use App\Models\RegionalOutlet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionalOutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (RegionalOutlet::count() > 0) {
            return;
        }

        $outlets = [
            // ==================== BALI ====================
            [
                'id_outlet' => 'OUT-DPS-001',
                'nama_outlet' => 'Outlet GraPARI Denpasar Teuku Umar',
                'longitude' => 115.2078,
                'latitude' => -8.6782,
                'kabupaten' => 'Kota Denpasar',
                'cluster' => 'Cluster Denpasar Kota',
                'branch' => 'Branch Denpasar',
                'total_omzet' => 245000000,
                'flag_omzet' => 5.40, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-DPS-002',
                'nama_outlet' => 'Outlet Cell Sanur Beach',
                'longitude' => 115.2625,
                'latitude' => -8.6881,
                'kabupaten' => 'Kota Denpasar',
                'cluster' => 'Cluster Denpasar Selatan',
                'branch' => 'Branch Denpasar',
                'total_omzet' => 180000000,
                'flag_omzet' => 2.10, // Kuning (<= 3%)
            ],
            [
                'id_outlet' => 'OUT-DPS-003',
                'nama_outlet' => 'Outlet Gatot Subroto Ponsel',
                'longitude' => 115.2250,
                'latitude' => -8.6360,
                'kabupaten' => 'Kota Denpasar',
                'cluster' => 'Cluster Denpasar Utara',
                'branch' => 'Branch Denpasar',
                'total_omzet' => 95000000,
                'flag_omzet' => -1.85, // Hitam (< 0%)
            ],
            [
                'id_outlet' => 'OUT-BDG-001',
                'nama_outlet' => 'Outlet Kuta Central Sunset',
                'longitude' => 115.1785,
                'latitude' => -8.7125,
                'kabupaten' => 'Badung',
                'cluster' => 'Cluster Kuta Badung',
                'branch' => 'Branch Denpasar',
                'total_omzet' => 310000000,
                'flag_omzet' => 6.75, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-BDG-002',
                'nama_outlet' => 'Outlet Canggu Echo Phone',
                'longitude' => 115.1380,
                'latitude' => -8.6510,
                'kabupaten' => 'Badung',
                'cluster' => 'Cluster Badung Utara',
                'branch' => 'Branch Denpasar',
                'total_omzet' => 140000000,
                'flag_omzet' => 0.00, // Merah (= 0%)
            ],
            [
                'id_outlet' => 'OUT-BDG-003',
                'nama_outlet' => 'Outlet Nusa Dua ITDC',
                'longitude' => 115.2280,
                'latitude' => -8.8020,
                'kabupaten' => 'Badung',
                'cluster' => 'Cluster Badung Selatan',
                'branch' => 'Branch Denpasar',
                'total_omzet' => 220000000,
                'flag_omzet' => 2.80, // Kuning (<= 3%)
            ],
            [
                'id_outlet' => 'OUT-GNY-001',
                'nama_outlet' => 'Outlet Ubud Monkey Forest',
                'longitude' => 115.2600,
                'latitude' => -8.5120,
                'kabupaten' => 'Gianyar',
                'cluster' => 'Cluster Gianyar Ubud',
                'branch' => 'Branch Gianyar',
                'total_omzet' => 165000000,
                'flag_omzet' => 4.20, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-GNY-002',
                'nama_outlet' => 'Outlet Gianyar Kota Sukawati',
                'longitude' => 115.2850,
                'latitude' => -8.5420,
                'kabupaten' => 'Gianyar',
                'cluster' => 'Cluster Gianyar Selatan',
                'branch' => 'Branch Gianyar',
                'total_omzet' => 88000000,
                'flag_omzet' => -2.40, // Hitam (< 0%)
            ],
            [
                'id_outlet' => 'OUT-TBN-001',
                'nama_outlet' => 'Outlet Tabanan Kota ByPass',
                'longitude' => 115.1270,
                'latitude' => -8.5380,
                'kabupaten' => 'Tabanan',
                'cluster' => 'Cluster Tabanan',
                'branch' => 'Branch Tabanan',
                'total_omzet' => 115000000,
                'flag_omzet' => 1.50, // Kuning (<= 3%)
            ],
            [
                'id_outlet' => 'OUT-BLG-001',
                'nama_outlet' => 'Outlet Singaraja Pantai Penimbangan',
                'longitude' => 115.0920,
                'latitude' => -8.1150,
                'kabupaten' => 'Buleleng',
                'cluster' => 'Cluster Buleleng Kota',
                'branch' => 'Branch Singaraja',
                'total_omzet' => 175000000,
                'flag_omzet' => 3.80, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-BLG-002',
                'nama_outlet' => 'Outlet Seririt Buleleng Barat',
                'longitude' => 114.9350,
                'latitude' => -8.1920,
                'kabupaten' => 'Buleleng',
                'cluster' => 'Cluster Buleleng Barat',
                'branch' => 'Branch Singaraja',
                'total_omzet' => 72000000,
                'flag_omzet' => 0.00, // Merah (= 0%)
            ],
            [
                'id_outlet' => 'OUT-JMB-001',
                'nama_outlet' => 'Outlet Negara Gilimanuk Port',
                'longitude' => 114.4420,
                'latitude' => -8.1650,
                'kabupaten' => 'Jembrana',
                'cluster' => 'Cluster Jembrana',
                'branch' => 'Branch Tabanan',
                'total_omzet' => 128000000,
                'flag_omzet' => 2.45, // Kuning (<= 3%)
            ],
            [
                'id_outlet' => 'OUT-KLK-001',
                'nama_outlet' => 'Outlet Nusa Penida Toyapakeh',
                'longitude' => 115.4850,
                'latitude' => -8.6850,
                'kabupaten' => 'Klungkung',
                'cluster' => 'Cluster Klungkung Kepulauan',
                'branch' => 'Branch Gianyar',
                'total_omzet' => 92000000,
                'flag_omzet' => 4.90, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-KRA-001',
                'nama_outlet' => 'Outlet Karangasem Amlapura Central',
                'longitude' => 115.6080,
                'latitude' => -8.4450,
                'kabupaten' => 'Karangasem',
                'cluster' => 'Cluster Karangasem',
                'branch' => 'Branch Gianyar',
                'total_omzet' => 64000000,
                'flag_omzet' => -3.10, // Hitam (< 0%)
            ],

            // ==================== LOMBOK (NTB) ====================
            [
                'id_outlet' => 'OUT-MTR-001',
                'nama_outlet' => 'Outlet GraPARI Mataram Pejanggik',
                'longitude' => 116.1210,
                'latitude' => -8.5830,
                'kabupaten' => 'Kota Mataram',
                'cluster' => 'Cluster Mataram Kota',
                'branch' => 'Branch Mataram',
                'total_omzet' => 280000000,
                'flag_omzet' => 5.90, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-MTR-002',
                'nama_outlet' => 'Outlet Ampenan Heritage',
                'longitude' => 116.0820,
                'latitude' => -8.5720,
                'kabupaten' => 'Kota Mataram',
                'cluster' => 'Cluster Mataram Barat',
                'branch' => 'Branch Mataram',
                'total_omzet' => 110000000,
                'flag_omzet' => 1.80, // Kuning (<= 3%)
            ],
            [
                'id_outlet' => 'OUT-LOB-001',
                'nama_outlet' => 'Outlet Senggigi Tourist Hub',
                'longitude' => 116.0490,
                'latitude' => -8.4980,
                'kabupaten' => 'Lombok Barat',
                'cluster' => 'Cluster Lombok Barat',
                'branch' => 'Branch Mataram',
                'total_omzet' => 135000000,
                'flag_omzet' => 3.20, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-LOT-001',
                'nama_outlet' => 'Outlet Praya Mandalika Airport',
                'longitude' => 116.2760,
                'latitude' => -8.7610,
                'kabupaten' => 'Lombok Tengah',
                'cluster' => 'Cluster Lombok Tengah',
                'branch' => 'Branch Mataram',
                'total_omzet' => 195000000,
                'flag_omzet' => 7.10, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-LOT-002',
                'nama_outlet' => 'Outlet Kuta Mandalika Circuit',
                'longitude' => 116.2890,
                'latitude' => -8.8950,
                'kabupaten' => 'Lombok Tengah',
                'cluster' => 'Cluster Mandalika',
                'branch' => 'Branch Mataram',
                'total_omzet' => 210000000,
                'flag_omzet' => 4.50, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-LOE-001',
                'nama_outlet' => 'Outlet Selong Lombok Timur',
                'longitude' => 116.5340,
                'latitude' => -8.6520,
                'kabupaten' => 'Lombok Timur',
                'cluster' => 'Cluster Lombok Timur',
                'branch' => 'Branch Mataram',
                'total_omzet' => 85000000,
                'flag_omzet' => 0.00, // Merah (= 0%)
            ],
            [
                'id_outlet' => 'OUT-LOU-001',
                'nama_outlet' => 'Outlet Tanjung Bangsal Gili',
                'longitude' => 116.1480,
                'latitude' => -8.3580,
                'kabupaten' => 'Lombok Utara',
                'cluster' => 'Cluster Lombok Utara',
                'branch' => 'Branch Mataram',
                'total_omzet' => 76000000,
                'flag_omzet' => -1.20, // Hitam (< 0%)
            ],

            // ==================== SUMBAWA (NTB) ====================
            [
                'id_outlet' => 'OUT-SBW-001',
                'nama_outlet' => 'Outlet Sumbawa Besar Hasanuddin',
                'longitude' => 117.4280,
                'latitude' => -8.5020,
                'kabupaten' => 'Sumbawa',
                'cluster' => 'Cluster Sumbawa',
                'branch' => 'Branch Sumbawa',
                'total_omzet' => 145000000,
                'flag_omzet' => 2.70, // Kuning (<= 3%)
            ],
            [
                'id_outlet' => 'OUT-KSB-001',
                'nama_outlet' => 'Outlet Taliwang Tambang',
                'longitude' => 116.8520,
                'latitude' => -8.7410,
                'kabupaten' => 'Sumbawa Barat',
                'cluster' => 'Cluster Sumbawa Barat',
                'branch' => 'Branch Sumbawa',
                'total_omzet' => 160000000,
                'flag_omzet' => 4.10, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-DMP-001',
                'nama_outlet' => 'Outlet Dompu Kota Beringin',
                'longitude' => 118.4610,
                'latitude' => -8.5360,
                'kabupaten' => 'Dompu',
                'cluster' => 'Cluster Dompu',
                'branch' => 'Branch Bima',
                'total_omzet' => 68000000,
                'flag_omzet' => -0.80, // Hitam (< 0%)
            ],
            [
                'id_outlet' => 'OUT-BIM-001',
                'nama_outlet' => 'Outlet Kota Bima Soekarno Hatta',
                'longitude' => 118.7280,
                'latitude' => -8.4620,
                'kabupaten' => 'Kota Bima',
                'cluster' => 'Cluster Bima Kota',
                'branch' => 'Branch Bima',
                'total_omzet' => 185000000,
                'flag_omzet' => 3.40, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-BIM-002',
                'nama_outlet' => 'Outlet Sape Pelabuhan Bima',
                'longitude' => 119.0050,
                'latitude' => -8.5720,
                'kabupaten' => 'Bima',
                'cluster' => 'Cluster Bima Timur',
                'branch' => 'Branch Bima',
                'total_omzet' => 82000000,
                'flag_omzet' => 1.10, // Kuning (<= 3%)
            ],

            // ==================== FLORES (NTT) ====================
            [
                'id_outlet' => 'OUT-LBJ-001',
                'nama_outlet' => 'Outlet Labuan Bajo Marina Komodo',
                'longitude' => 119.8820,
                'latitude' => -8.4980,
                'kabupaten' => 'Manggarai Barat',
                'cluster' => 'Cluster Labuan Bajo',
                'branch' => 'Branch Flores Barat',
                'total_omzet' => 320000000,
                'flag_omzet' => 8.40, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-RTG-001',
                'nama_outlet' => 'Outlet Ruteng Manggarai Tengah',
                'longitude' => 120.4680,
                'latitude' => -8.6180,
                'kabupaten' => 'Manggarai',
                'cluster' => 'Cluster Manggarai',
                'branch' => 'Branch Flores Barat',
                'total_omzet' => 95000000,
                'flag_omzet' => 0.00, // Merah (= 0%)
            ],
            [
                'id_outlet' => 'OUT-BJW-001',
                'nama_outlet' => 'Outlet Bajawa Ngada Alun-Alun',
                'longitude' => 120.9650,
                'latitude' => -8.7920,
                'kabupaten' => 'Ngada',
                'cluster' => 'Cluster Ngada Nagekeo',
                'branch' => 'Branch Ende',
                'total_omzet' => 74000000,
                'flag_omzet' => 2.30, // Kuning (<= 3%)
            ],
            [
                'id_outlet' => 'OUT-END-001',
                'nama_outlet' => 'Outlet GraPARI Ende Kelimutu',
                'longitude' => 121.6580,
                'latitude' => -8.8410,
                'kabupaten' => 'Ende',
                'cluster' => 'Cluster Ende',
                'branch' => 'Branch Ende',
                'total_omzet' => 140000000,
                'flag_omzet' => 3.15, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-MOF-001',
                'nama_outlet' => 'Outlet Maumere Sikka Pelabuhan',
                'longitude' => 122.2150,
                'latitude' => -8.6210,
                'kabupaten' => 'Sikka',
                'cluster' => 'Cluster Sikka',
                'branch' => 'Branch Ende',
                'total_omzet' => 125000000,
                'flag_omzet' => -2.10, // Hitam (< 0%)
            ],
            [
                'id_outlet' => 'OUT-LAR-001',
                'nama_outlet' => 'Outlet Larantuka Flores Timur',
                'longitude' => 122.9850,
                'latitude' => -8.3450,
                'kabupaten' => 'Flores Timur',
                'cluster' => 'Cluster Flotim Lembata',
                'branch' => 'Branch Ende',
                'total_omzet' => 88000000,
                'flag_omzet' => 1.95, // Kuning (<= 3%)
            ],

            // ==================== SUMBA (NTT) ====================
            [
                'id_outlet' => 'OUT-WGP-001',
                'nama_outlet' => 'Outlet Waingapu Sumba Timur',
                'longitude' => 120.2680,
                'latitude' => -9.6580,
                'kabupaten' => 'Sumba Timur',
                'cluster' => 'Cluster Sumba Timur',
                'branch' => 'Branch Kupang',
                'total_omzet' => 110000000,
                'flag_omzet' => 2.85, // Kuning (<= 3%)
            ],
            [
                'id_outlet' => 'OUT-WKB-001',
                'nama_outlet' => 'Outlet Waikabubak Sumba Barat',
                'longitude' => 119.3180,
                'latitude' => -9.6350,
                'kabupaten' => 'Sumba Barat',
                'cluster' => 'Cluster Sumba Barat',
                'branch' => 'Branch Kupang',
                'total_omzet' => 78000000,
                'flag_omzet' => -1.40, // Hitam (< 0%)
            ],

            // ==================== TIMOR, ALOR, ROTE (NTT) ====================
            [
                'id_outlet' => 'OUT-KPG-001',
                'nama_outlet' => 'Outlet GraPARI Kupang Sudirman',
                'longitude' => 123.6040,
                'latitude' => -10.1750,
                'kabupaten' => 'Kota Kupang',
                'cluster' => 'Cluster Kupang Kota',
                'branch' => 'Branch Kupang',
                'total_omzet' => 290000000,
                'flag_omzet' => 6.20, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-KPG-002',
                'nama_outlet' => 'Outlet Kupang Oebobo Mart',
                'longitude' => 123.6210,
                'latitude' => -10.1580,
                'kabupaten' => 'Kota Kupang',
                'cluster' => 'Cluster Kupang Timur',
                'branch' => 'Branch Kupang',
                'total_omzet' => 130000000,
                'flag_omzet' => 0.00, // Merah (= 0%)
            ],
            [
                'id_outlet' => 'OUT-SOE-001',
                'nama_outlet' => 'Outlet Soe Timor Tengah Selatan',
                'longitude' => 124.2810,
                'latitude' => -9.8620,
                'kabupaten' => 'Timor Tengah Selatan',
                'cluster' => 'Cluster TTS TTU',
                'branch' => 'Branch Kupang',
                'total_omzet' => 84000000,
                'flag_omzet' => 1.70, // Kuning (<= 3%)
            ],
            [
                'id_outlet' => 'OUT-ATB-001',
                'nama_outlet' => 'Outlet Atambua Belu Border Motaain',
                'longitude' => 124.8950,
                'latitude' => -9.1080,
                'kabupaten' => 'Belu',
                'cluster' => 'Cluster Belu Malaka',
                'branch' => 'Branch Kupang',
                'total_omzet' => 155000000,
                'flag_omzet' => 4.60, // Hijau (> 3%)
            ],
            [
                'id_outlet' => 'OUT-ALR-001',
                'nama_outlet' => 'Outlet Kalabahi Alor Bahari',
                'longitude' => 124.5210,
                'latitude' => -8.2190,
                'kabupaten' => 'Alor',
                'cluster' => 'Cluster Alor',
                'branch' => 'Branch Kupang',
                'total_omzet' => 69000000,
                'flag_omzet' => -2.80, // Hitam (< 0%)
            ],
            [
                'id_outlet' => 'OUT-RTE-001',
                'nama_outlet' => 'Outlet Baa Rote Ndao South Point',
                'longitude' => 123.0850,
                'latitude' => -10.7350,
                'kabupaten' => 'Rote Ndao',
                'cluster' => 'Cluster Rote Sabu',
                'branch' => 'Branch Kupang',
                'total_omzet' => 72000000,
                'flag_omzet' => 2.15, // Kuning (<= 3%)
            ],
        ];

        foreach ($outlets as $outlet) {
            unset($outlet['nama_outlet']);
            RegionalOutlet::create($outlet);
        }
    }
}
