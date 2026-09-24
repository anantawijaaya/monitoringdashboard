<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Test CLI command php artisan db:backup creates a backup file.
     */
    public function test_artisan_db_backup_command_creates_backup_file(): void
    {
        $exitCode = Artisan::call('db:backup');
        $this->assertEquals(0, $exitCode);

        $backupDir = storage_path('app/backups');
        $this->assertTrue(File::exists($backupDir));

        $files = File::files($backupDir);
        $this->assertNotEmpty($files);
    }

    /**
     * Test admin user can access Admin Backup & Restore web page.
     */
    public function test_admin_user_can_access_backup_management_page(): void
    {
        $admin = User::where('role', 'user')->first() ?? User::where('role', 'ADMIN')->first() ?? User::first();

        $response = $this->actingAs($admin)->get('/admin/backups');

        $response->assertStatus(200);
        $response->assertSee('Backup');
        $response->assertSee('Restore Data');
        $response->assertSee('Back Up Data Baru');
    }

    /**
     * Test visitor user cannot access Admin Backup & Restore page.
     */
    public function test_visitor_user_cannot_access_backup_management_page(): void
    {
        $visitor = User::where('role', 'visitor')->first();
        if (!$visitor) {
            $visitor = User::factory()->create(['role' => 'visitor']);
        }

        $response = $this->actingAs($visitor)->get('/admin/backups');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * Test admin user can trigger instant backup via web route.
     */
    public function test_admin_user_can_create_backup_via_web_action(): void
    {
        $admin = User::where('role', 'user')->first() ?? User::first();

        $response = $this->actingAs($admin)->post('/admin/backups/create');

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * Test admin user can restore database from uploaded SQL file.
     */
    public function test_admin_user_can_restore_from_uploaded_file(): void
    {
        $admin = User::where('role', 'user')->first() ?? User::first();

        $sqlContent = "-- Test Restore SQL Dump\n"
                    . "CREATE TABLE IF NOT EXISTS backup_test_dummy (id INT PRIMARY KEY, name VARCHAR(255));\n"
                    . "INSERT INTO backup_test_dummy VALUES (1, 'Test Data');\n";

        $file = UploadedFile::fake()->createWithContent('test_restore.sql', $sqlContent);

        $response = $this->actingAs($admin)->post('/admin/backups/restore', [
            'backup_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
