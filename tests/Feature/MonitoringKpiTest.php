<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringKpiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    protected function getAdminUser()
    {
        return User::where('role', 'user')->first() ?? User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
    }

    protected function getVisitorUser()
    {
        return User::where('role', 'visitor')->first() ?? User::factory()->create(['role' => 'visitor']);
    }

    /**
     * 1. Test Sub Menu Peta Regional
     */
    public function test_regional_map_routes_render_successfully(): void
    {
        $user = $this->getAdminUser();

        // Index page
        $response = $this->actingAs($user)->get('/regional-map');
        $response->assertStatus(200);
        $response->assertSee('PETA SEBARAN OUTLET');

        // Markers JSON
        $jsonResponse = $this->actingAs($user)->get('/regional-map/markers');
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonStructure(['data']);

        // Template Download
        $templateResponse = $this->actingAs($user)->get('/regional-map/template');
        $templateResponse->assertStatus(200);

        // Export CSV
        $exportResponse = $this->actingAs($user)->get('/regional-map/export');
        $exportResponse->assertStatus(200);
    }

    public function test_regional_map_import_file_supports_csv_and_html_table(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        // Test CSV import with duplicate IDs and missing coordinates
        $csvContent = "ID Outlet,Longitude,Latitude,Kabupaten,Cluster,Branch,Total Omzet,Flag Omzet\n"
                    . "OUT-TEST-1,115.21,-8.67,Kota Denpasar,BALI BARAT,Branch Denpasar,150000000,5.4\n"
                    . "OUT-TEST-1,115.22,-8.68,Kota Denpasar,BALI BARAT,Branch Denpasar,200000000,2.1\n"
                    . "OUT-TEST-2,116.12,-8.58,Kota Mataram,LOMBOK,Branch Mataram,180000000,<0%\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('test_regional.csv', $csvContent);

        $response = $this->actingAs($admin)->post('/regional-map/import', [
            'file' => $file,
            'import_mode' => 'replace',
        ]);

        $response->assertRedirect('/regional-map');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('regional_outlets', ['id_outlet' => 'OUT-TEST-1']);
        $this->assertDatabaseHas('regional_outlets', ['id_outlet' => 'OUT-TEST-1-2']);
        $this->assertDatabaseHas('regional_outlets', ['id_outlet' => 'OUT-TEST-2']);
    }

    /**
     * 2. Test Sub Menu Hirarki
     */
    public function test_hierarchy_routes_render_successfully(): void
    {
        $user = $this->getAdminUser();

        // Index page
        $response = $this->actingAs($user)->get('/hierarchy');
        $response->assertStatus(200);
        $response->assertSee('HIRARKI REGIONAL BALI NUSRA');

        // Template Download
        $templateResponse = $this->actingAs($user)->get('/hierarchy/template');
        $templateResponse->assertStatus(200);

        // Export Excel
        $exportResponse = $this->actingAs($user)->get('/hierarchy/export');
        $exportResponse->assertStatus(200);
    }

    /**
     * 3. Test Sub Menu Revenue
     */
    public function test_revenue_manage_routes_render_successfully(): void
    {
        $user = $this->getAdminUser();

        // Index page
        $response = $this->actingAs($user)->get('/revenue/manage');
        $response->assertStatus(200);
        $response->assertSee('Kelola Revenue');

        // Template Download
        $templateResponse = $this->actingAs($user)->get('/revenue/template');
        $templateResponse->assertStatus(200);

        // Export CSV
        $exportResponse = $this->actingAs($user)->get('/revenue/export');
        $exportResponse->assertStatus(200);
    }

    /**
     * 4. Test Sub Menu Peringkat
     */
    public function test_ranking_routes_render_successfully(): void
    {
        $user = $this->getAdminUser();

        // Index page
        $response = $this->actingAs($user)->get('/ranking');
        $response->assertStatus(200);
        $response->assertSee('Peringkat Revenue Regional Bali Nusra');

        // Template Download
        $templateResponse = $this->actingAs($user)->get('/ranking/template');
        $templateResponse->assertStatus(200);

        // Export
        $exportResponse = $this->actingAs($user)->get('/ranking/export');
        $exportResponse->assertStatus(200);
    }

    /**
     * 5. Test Sub Menu Kelas KPI
     */
    public function test_kelas_kpi_routes_render_successfully(): void
    {
        $user = $this->getAdminUser();

        // Index page
        $response = $this->actingAs($user)->get('/kelas-kpi');
        $response->assertStatus(200);
        $response->assertSee('KELAS KPI CLUSTER BALI NUSRA');

        // Template Download
        $templateResponse = $this->actingAs($user)->get('/kelas-kpi/template');
        $templateResponse->assertStatus(200);

        // Export
        $exportResponse = $this->actingAs($user)->get('/kelas-kpi/export');
        $exportResponse->assertStatus(200);
    }

    /**
     * 6. Test Unauthenticated & Visitor Access Restrictions
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/hierarchy');
        $response->assertRedirect('/login');

        $response = $this->get('/revenue/manage');
        $response->assertRedirect('/login');

        $response = $this->get('/regional-map');
        $response->assertRedirect('/login');
    }
}
