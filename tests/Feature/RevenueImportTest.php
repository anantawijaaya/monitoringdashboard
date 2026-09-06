<?php

namespace Tests\Feature;

use App\Models\ClusterRevenue;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RevenueImportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_exact_screenshot_columns_import_success()
    {
        $user = User::where('email', 'admin.balinusra@telkomsel.co.id')->first() ?? User::factory()->create(['role' => 'user']);

        // KABUPATEN;CLUSTER;BULAN;TAHUN;TARGET REV ALL;MTD REV ALL;TARGET BB;MTD BB;TARGET PV;MTD PV;Target RGB All;MTD RGB All;Data Bln Sebelumnya;Data Bln Sekarang
        $csvContent = "KABUPATEN;CLUSTER;BULAN;TAHUN;TARGET REV ALL;MTD REV ALL;TARGET BB;MTD BB;TARGET PV;MTD PV;Target RGB All;MTD RGB All;Data Bln Sebelumnya;Data Bln Sekarang\n" .
            "BULELENG;BALI BARAT;Agustus 2026;2026;4250000000;4180000000;3870000000;3810000000;245000000;228000000;135000000;142000000;4100000000;4180000000\n" .
            "BADUNG;BALI TENGAH;Agustus 2026;2026;22500000000;22350000000;20000000000;19950000000;1270000000;1200000000;1230000000;1200000000;21800000000;22350000000";

        $file = UploadedFile::fake()->createWithContent('DATA UNTUK MENU KELOLA REVENUE FIX.csv', $csvContent);

        $response = $this->actingAs($user)->post('/revenue/import', [
            'file' => $file,
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $buleleng = ClusterRevenue::where('kabupaten', 'BULELENG')->first();
        $this->assertNotNull($buleleng);
        $this->assertEquals('BALI BARAT', $buleleng->cluster_name);
        $this->assertEquals(4250000000, floatval($buleleng->target_revenue_all));
        $this->assertEquals(4180000000, floatval($buleleng->mtd_revenue_all));
        $this->assertEquals(3870000000, floatval($buleleng->target_broadband));
        $this->assertEquals(3810000000, floatval($buleleng->mtd_broadband));
        $this->assertEquals(245000000, floatval($buleleng->target_redeem));
        $this->assertEquals(228000000, floatval($buleleng->mtd_redeem));
        $this->assertEquals(135000000, floatval($buleleng->target_rgb));
        $this->assertEquals(142000000, floatval($buleleng->mtd_rgb));
    }

    public function test_scientific_notation_and_currency_formatting_import()
    {
        $user = User::where('email', 'admin.balinusra@telkomsel.co.id')->first() ?? User::factory()->create(['role' => 'user']);

        $csvContent = "KABUPATEN;CLUSTER;TARGET REV ALL;MTD REV ALL\n" .
            "TABANAN;BALI BARAT;3.65E+09;3.56e9\n" .
            "GIANYAR;BALI TIMUR;Rp 2.350.000.000,00;Rp 2.300.000.000,00";

        $file = UploadedFile::fake()->createWithContent('scientific_import.csv', $csvContent);

        $response = $this->actingAs($user)->post('/revenue/import', [
            'file' => $file,
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $tabanan = ClusterRevenue::where('kabupaten', 'TABANAN')->first();
        $this->assertNotNull($tabanan);
        $this->assertEquals(3650000000, floatval($tabanan->target_revenue_all));
        $this->assertEquals(3560000000, floatval($tabanan->mtd_revenue_all));

        $gianyar = ClusterRevenue::where('kabupaten', 'GIANYAR')->first();
        $this->assertNotNull($gianyar);
        $this->assertEquals(2350000000, floatval($gianyar->target_revenue_all));
        $this->assertEquals(2300000000, floatval($gianyar->mtd_revenue_all));
    }

    public function test_stacked_headers_and_banner_title_import()
    {
        $user = User::where('email', 'admin.balinusra@telkomsel.co.id')->first() ?? User::factory()->create(['role' => 'user']);

        $csvContent = "LAPORAN REVENUE REGIONAL BALI NUSRA 2026\n" .
            "Cluster;Kabupaten;Revenue ALL;Revenue ALL;Broadband;Broadband;Redeem PV;Redeem PV;Catatan\n" .
            ";;Target;MTD;Target;MTD;Target;MTD;\n" .
            "BALI TIMUR;GIANYAR;3000000000;2900000000;2500000000;2400000000;500000000;500000000;Optimal";

        $file = UploadedFile::fake()->createWithContent('test_stacked.csv', $csvContent);

        $response = $this->actingAs($user)->post('/revenue/import', [
            'file' => $file,
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $recordGianyar = ClusterRevenue::where('kabupaten', 'GIANYAR')->first();
        $this->assertNotNull($recordGianyar);
        $this->assertEquals('BALI TIMUR', $recordGianyar->cluster_name);
        $this->assertEquals(3000000000, floatval($recordGianyar->target_revenue_all));
        $this->assertEquals(2900000000, floatval($recordGianyar->mtd_revenue_all));
    }

    public function test_html_table_export_import()
    {
        $user = User::where('email', 'admin.balinusra@telkomsel.co.id')->first() ?? User::factory()->create(['role' => 'user']);

        $htmlContent = "<html><body><table>" .
            "<tr><th>Cluster</th><th>Kabupaten</th><th>Target Revenue</th><th>MTD Revenue</th></tr>" .
            "<tr><td>ENDE SIKKA</td><td>ENDE</td><td>2000000000</td><td>1950000000</td></tr>" .
            "</table></body></html>";

        $file = UploadedFile::fake()->createWithContent('report_export.xls', $htmlContent);

        $response = $this->actingAs($user)->post('/revenue/import', [
            'file' => $file,
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $recordEnde = ClusterRevenue::where('kabupaten', 'ENDE')->first();
        $this->assertNotNull($recordEnde);
        $this->assertEquals(2000000000, floatval($recordEnde->target_revenue_all));
        $this->assertEquals(1950000000, floatval($recordEnde->mtd_revenue_all));
    }

    public function test_kota_mataram_kota_bima_kota_kupang_import_rgb()
    {
        $user = User::where('email', 'admin.balinusra@telkomsel.co.id')->first() ?? User::factory()->create(['role' => 'user']);

        $csvContent = "KABUPATEN;CLUSTER;BULAN;TAHUN;TARGET REV ALL;MTD REV ALL;TARGET BB;MTD BB;TARGET PV;MTD PV;Target RGB All;MTD RGB All;Data Bln Sebelumnya;Data Bln Sekarang\n" .
            "KOTA MATARAM;LOMBOK;Agustus 2026;2026;1250000000;1220000000;1165000000;1140000000;290000000;250000000;85000000;82000000;1180000000;1220000000\n" .
            "KOTA BIMA;SUMBAWA TIMUR;Agustus 2026;2026;2150000000;1960000000;1900000000;1745000000;66000000;56000000;65000000;58000000;1890000000;1960000000\n" .
            "KOTA KUPANG;KUPANG ROTE;Agustus 2026;2026;3850000000;3590000000;3500000000;3260000000;6500000;4740000;125000000;115000000;3480000000;3590000000";

        $file = UploadedFile::fake()->createWithContent('cities_rgb_import.csv', $csvContent);

        $response = $this->actingAs($user)->post('/revenue/import', [
            'file' => $file,
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert KOTA MATARAM
        $mataram = ClusterRevenue::where('kabupaten', 'KOTA MATARAM')->first();
        $this->assertNotNull($mataram);
        $this->assertEquals(85000000, floatval($mataram->target_rgb));
        $this->assertEquals(82000000, floatval($mataram->mtd_rgb));

        // Assert NO duplicate MATARAM row
        $duplicateMataram = ClusterRevenue::where('kabupaten', 'MATARAM')->first();
        $this->assertNull($duplicateMataram);

        // Assert KOTA BIMA
        $kotaBima = ClusterRevenue::where('kabupaten', 'KOTA BIMA')->first();
        $this->assertNotNull($kotaBima);
        $this->assertEquals(65000000, floatval($kotaBima->target_rgb));
        $this->assertEquals(58000000, floatval($kotaBima->mtd_rgb));

        // Assert KOTA KUPANG
        $kotaKupang = ClusterRevenue::where('kabupaten', 'KOTA KUPANG')->first();
        $this->assertNotNull($kotaKupang);
        $this->assertEquals(125000000, floatval($kotaKupang->target_rgb));
        $this->assertEquals(115000000, floatval($kotaKupang->mtd_rgb));
    }
}
