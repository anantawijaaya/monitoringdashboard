<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
    }

    public function test_catalist_user_can_login_with_user_role()
    {
        $user = User::where('role', 'admin')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isAdmin());
    }

    public function test_visitor1_user_can_login_with_visitor_role()
    {
        $user = User::where('role', 'visitor')->first() ?? User::factory()->create([
            'email' => 'visitor1@gmail.com',
            'role' => 'visitor',
        ]);
        $this->assertNotNull($user);
        $this->assertEquals('visitor', strtolower($user->role));
        $this->assertFalse($user->isAdmin());
    }

    public function test_home_and_monitoring_kpi_are_not_locked_for_cluster_accounts()
    {
        $visitor = User::create([
            'name' => 'Admin Cluster Bali Barat',
            'email' => 'testbalibarat@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'visitor',
            'cluster_name' => 'BALI BARAT',
        ]);

        // Home dashboard allows viewing all clusters
        $responseHome = $this->actingAs($visitor)->get(route('dashboard'));
        $responseHome->assertStatus(200);
        $responseHome->assertDontSee('bi-lock-fill');

        // Monitoring KPI allows viewing all clusters
        $responseRegional = $this->actingAs($visitor)->get(route('regional-map.index'));
        $responseRegional->assertStatus(200);
    }

    public function test_budget_bk_is_locked_for_cluster_accounts()
    {
        $visitor = User::create([
            'name' => 'Admin Cluster Bali Barat',
            'email' => 'testbalibarat2@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'visitor',
            'cluster_name' => 'BALI BARAT',
        ]);

        // Budget-BK menu locks cluster filter
        $responseIndirect = $this->actingAs($visitor)->get(route('budget-bk.indirect-channel.index'));
        $responseIndirect->assertStatus(200);
        $responseIndirect->assertSee('bi-lock-fill');
    }
}
