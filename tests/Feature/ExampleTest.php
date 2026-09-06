<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_revenue_manage_page_renders_separated_columns(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get('/revenue/manage');

        $response->assertStatus(200);
        $response->assertSee('1. CLUSTER');
        $response->assertSee('KABUPATEN / KOTA');
        $response->assertSee('BALI BARAT');
        $response->assertSee('BULELENG');
        $response->assertSee('JEMBRANA');
        $response->assertSee('BALI TENGAH');
        $response->assertSee('BADUNG');
        $response->assertSee('KOTA DENPASAR');
        $response->assertSee('Tambah Data Manual');
        $response->assertSee('Import CSV / Excel');
        $response->assertSee('id="manualModal"', false);
        $response->assertSee('id="importModal"', false);
    }

    public function test_dashboard_renders_cleanly_without_details_table(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Telkomsel Regional Bali Nusra');
        $response->assertDontSee('Daftar Rincian Target vs MTD & Perangkingan (Database)');
    }

    public function test_store_manual_per_kabupaten(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->post('/revenue/manual', [
            'kabupaten' => 'TABANAN',
            'cluster_name' => 'BALI BARAT',
            'period_month' => 'AGUSTUS',
            'period_year' => 2026,
            'target_revenue_all' => 3500000000,
            'mtd_revenue_all' => 3450000000,
            'target_broadband' => 3000000000,
            'mtd_broadband' => 2950000000,
            'target_redeem' => 500000000,
            'mtd_redeem' => 500000000,
            'revenue_last_month' => 3300000000,
            'revenue_current_month' => 3450000000,
            'notes' => 'Test input per kabupaten',
        ]);

        $this->assertDatabaseHas('revenue_data', [
            'kabupaten' => 'TABANAN',
            'cluster_name' => 'BALI BARAT',
            'period_month' => 'AGUSTUS',
            'period_year' => 2026,
            'mtd_revenue_all' => 3450000000,
        ]);
    }

    public function test_ranking_page_renders_leaderboards_and_podium(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get('/ranking');

        $response->assertStatus(200);
        $response->assertSee('Peringkat Revenue Regional Bali Nusra');
        $response->assertSee('PERINGKAT REVENUE REGIONAL TERTINGGI');
        $response->assertSee('Klasemen Lengkap Revenue');
        $response->assertSee('JUARA 1 REGIONAL');
    }
}
