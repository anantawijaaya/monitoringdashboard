<?php

namespace Tests\Feature;

use App\Models\KelasKpi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class KelasKpiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Test authenticated user can access Monitoring KPI SBP (Kelas KPI) page.
     */
    public function test_kelas_kpi_index_page_renders_successfully(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get('/kelas-kpi');

        $response->assertStatus(200);
        $response->assertSee('Kelas KPI');
        $response->assertSee('Telkomsel Regional Bali Nusra');
    }

    /**
     * Test downloading CSV template for Kelas KPI import.
     */
    public function test_kelas_kpi_download_template_returns_csv_download(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get('/kelas-kpi/template');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    /**
     * Test exporting Kelas KPI data stream.
     */
    public function test_kelas_kpi_export_returns_csv_download(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get('/kelas-kpi/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    /**
     * Test Kelas KPI database record creation and query.
     */
    public function test_kelas_kpi_database_record_creation_and_query(): void
    {
        $kpi = KelasKpi::create([
            'cluster' => 'TEST_CLUSTER_KPI',
            'new_cluster' => 'TEST_CLUSTER_KPI',
            'periode' => '2026-08',
            'target_rev_all' => 10000000000,
            'actual_rev_all' => 9500000000,
            'ach_rev_all' => 95.0,
            'final_score' => 1.15,
        ]);

        $this->assertDatabaseHas('kelas_kpis', [
            'cluster' => 'TEST_CLUSTER_KPI',
        ]);

        $this->assertEquals('TEST_CLUSTER_KPI', $kpi->cluster);
        $this->assertEquals(95.0, floatval($kpi->ach_rev_all));
        $this->assertEquals(1.15, floatval($kpi->final_score));
    }

    /**
     * Test admin user can import Kelas KPI CSV file.
     */
    public function test_admin_user_can_import_kelas_kpi_file(): void
    {
        $admin = User::where('role', 'user')->first() ?? User::where('role', 'ADMIN')->first() ?? User::first();

        $csvContent = "cluster,periode,target rev all,actual rev all,target rev bb,actual rev bb,target rev pv,actual rev pv,target rgb,actual rgb,omzet rev m1,mtd m1,mtd,outlet pjp,outlet pjp growth\n"
                    . "BALI BARAT,2026-08,24133301567,22145064457,14500000000,13800000000,5000000000,4800000000,4633301567,3545064457,21800000000,21500000000,22145064457,1250,4.2\n";

        $file = UploadedFile::fake()->createWithContent('kpi_test.csv', $csvContent);

        $response = $this->actingAs($admin)->post('/kelas-kpi/import', [
            'file' => $file,
            'mode' => 'append',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * Test unauthenticated guest user is redirected to login.
     */
    public function test_guest_user_cannot_access_kelas_kpi_dashboard(): void
    {
        $response = $this->get('/kelas-kpi');

        $response->assertRedirect('/login');
    }
}
