<?php

namespace Tests\Feature;

use App\Models\RegionalOutlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RegionalMapImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
    }

    public function test_regional_map_import_csv_with_no_column_and_replace_mode()
    {
        $admin = User::first();

        $csvHeader = "No,ID Outlet,Longitude,Latitude,Kabupaten,Cluster,Branch,Total Omzet,Flag Omzet\n";
        $csvRow1 = "1,OUT-TEST-001,115.2126,-8.6705,Kota Denpasar,Cluster Denpasar,Branch Denpasar,150000000,4.5\n";
        $csvRow2 = "2,OUT-TEST-002,115.2200,-8.6800,Kota Denpasar,Cluster Denpasar,Branch Denpasar,200000000,-1.2\n";

        $fileContent = $csvHeader . $csvRow1 . $csvRow2;
        $file = UploadedFile::fake()->createWithContent('import_test.csv', $fileContent);

        $response = $this->actingAs($admin)
                         ->post(route('regional-map.import'), [
                             'file' => $file,
                             'import_mode' => 'replace',
                         ]);

        $response->assertRedirect(route('regional-map.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('regional_outlets', [
            'id_outlet' => 'OUT-TEST-001',
            'kabupaten' => 'Kota Denpasar',
            'total_omzet' => 150000000,
        ]);

        $this->assertDatabaseHas('regional_outlets', [
            'id_outlet' => 'OUT-TEST-002',
            'flag_omzet' => -1.2,
        ]);

        $this->assertEquals(2, RegionalOutlet::count());
    }

    public function test_regional_map_import_without_specifying_import_mode_defaults_to_replace()
    {
        $admin = User::first();

        $csvHeader = "id_outlet,longitude,latitude,kabupaten,cluster,branch,total_omzet,flag_omzet\n";
        $csvRow = "OUT-DEFAULT-01,115.2,-8.6,Badung,Cluster Badung,Branch Denpasar,100000000,2.5\n";

        $file = UploadedFile::fake()->createWithContent('import_default.csv', $csvHeader . $csvRow);

        $response = $this->actingAs($admin)
                         ->post(route('regional-map.import'), [
                             'file' => $file,
                         ]);

        $response->assertRedirect(route('regional-map.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('regional_outlets', [
            'id_outlet' => 'OUT-DEFAULT-01',
        ]);
    }
}
