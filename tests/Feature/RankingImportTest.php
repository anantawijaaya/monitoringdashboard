<?php

namespace Tests\Feature;

use App\Models\PeringkatData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RankingImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
    }

    public function test_ranking_import_csv_in_replace_mode_succeeds_without_transaction_error()
    {
        $admin = User::first();

        $csvContent = "CITY / KABUPATEN,CLUSTER,PERIODE,TARGET REV ALL,ACTUAL REV ALL,TARGET REV BB,ACTUAL REV BB,TARGET REV PV,ACTUAL REV PV,TARGET RGB,ACTUAL RGB,OMZET REV M1,MTD M1,MTD\n"
                    . "KLUNGKUNG,BALI TIMUR,Agustus 2026,10704969621,10800000000,5645764820,5513539874,1000000,1000000,500,520,10500000000,10500000000,10800000000\n"
                    . "BADUNG,BALI TENGAH,Agustus 2026,78368670539,80000000000,41474768541,42397702783,2000000,2000000,1000,1050,79000000000,79000000000,80000000000\n";

        $file = UploadedFile::fake()->createWithContent('ranking_test.csv', $csvContent);

        $response = $this->actingAs($admin)
                         ->post('/ranking/import', [
                             'file' => $file,
                             'mode' => 'replace',
                         ]);

        $response->assertRedirect('/ranking');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('peringkat_data', [
            'city' => 'KLUNGKUNG',
            'cluster' => 'BALI TIMUR',
        ]);

        $this->assertDatabaseHas('peringkat_data', [
            'city' => 'BADUNG',
            'cluster' => 'BALI TENGAH',
        ]);
    }
}
