<?php

namespace Tests\Feature;

use App\Models\CultureProgramAllocation;
use App\Models\CultureProgramExpense;
use App\Models\DirectSalesAllocation;
use App\Models\DirectSalesExpense;
use App\Models\IndirectChannelAllocation;
use App\Models\IndirectChannelExpense;
use App\Models\User;
use App\Services\BudgetCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BudgetBkTest extends TestCase
{
    use RefreshDatabase;

    protected BudgetCalculatorService $calculatorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
        $this->calculatorService = new BudgetCalculatorService();
    }

    /**
     * Test BudgetCalculatorService summary calculation logic using delta assertions.
     */
    public function test_budget_calculator_service_summary_calculation(): void
    {
        $testCluster = 'TEST_CLUSTER_A';

        // 1. Initial Summary for clean test cluster
        $initialSummary = $this->calculatorService->calculateSummary(
            IndirectChannelAllocation::class,
            IndirectChannelExpense::class,
            $testCluster
        );
        $this->assertEquals(0, $initialSummary['totalAlokasi']);
        $this->assertEquals(0, $initialSummary['totalRealisasi']);

        // 2. Create dummy allocation record
        IndirectChannelAllocation::create([
            'cluster' => $testCluster,
            'digital_marketing_budget' => 10000000,
            'cvm_program_budget' => 5000000,
            'engagement_outlet_budget' => 5000000,
            'branding_outlet_budget' => 5000000,
            'program_sales_outlet_budget' => 5000000,
            'voucher_games_budget' => 10000000,
            'total_budget' => 40000000,
        ]);

        // 3. Create dummy expenses (1 Disetujui, 1 Menunggu, 1 Ditolak)
        IndirectChannelExpense::create([
            'cluster' => $testCluster,
            'program_name' => 'Digital Marketing',
            'tanggal' => date('Y-m-d'),
            'waktu' => date('H:i:s'),
            'nominal_pengeluaran' => 5000000,
            'budget_program' => 10000000,
            'sisa_budget_program' => 5000000,
            'deskripsi' => 'Pengeluaran Disetujui',
            'status' => 'Disetujui',
            'evidence_path' => 'evidences/test1.pdf',
        ]);

        IndirectChannelExpense::create([
            'cluster' => $testCluster,
            'program_name' => 'Digital Marketing',
            'tanggal' => date('Y-m-d'),
            'waktu' => date('H:i:s'),
            'nominal_pengeluaran' => 3000000,
            'budget_program' => 10000000,
            'sisa_budget_program' => 2000000,
            'deskripsi' => 'Pengeluaran Menunggu',
            'status' => 'Menunggu',
            'evidence_path' => 'evidences/test2.pdf',
        ]);

        IndirectChannelExpense::create([
            'cluster' => $testCluster,
            'program_name' => 'Digital Marketing',
            'tanggal' => date('Y-m-d'),
            'waktu' => date('H:i:s'),
            'nominal_pengeluaran' => 2000000,
            'budget_program' => 10000000,
            'sisa_budget_program' => 8000000,
            'deskripsi' => 'Pengeluaran Ditolak',
            'status' => 'Ditolak',
            'evidence_path' => 'evidences/test3.pdf',
        ]);

        // Calculate summary for test cluster
        $summary = $this->calculatorService->calculateSummary(
            IndirectChannelAllocation::class,
            IndirectChannelExpense::class,
            $testCluster
        );

        // Assertions: Total Realisasi ONLY counts status = 'Disetujui' (5,000,000)
        $this->assertEquals(40000000, $summary['totalAlokasi']);
        $this->assertEquals(5000000, $summary['totalRealisasi']);
        $this->assertEquals(35000000, $summary['sisaBudget']);
        $this->assertEquals(12.5, $summary['totalRealisasiPersen']);
    }

    /**
     * Test BudgetCalculatorService program cards calculation and sorting.
     */
    public function test_budget_calculator_service_program_cards_and_sorting(): void
    {
        $testCluster = 'TEST_CLUSTER_B';

        DirectSalesAllocation::create([
            'cluster' => $testCluster,
            'direct_selling_budget' => 20000000,
            'grebek_poi_budget' => 10000000,
            'dls_skulid_budget' => 5000000,
            'total_budget' => 35000000,
        ]);

        DirectSalesExpense::create([
            'cluster' => $testCluster,
            'program_name' => 'Direct Selling',
            'tanggal' => date('Y-m-d'),
            'waktu' => date('H:i:s'),
            'nominal_pengeluaran' => 15000000,
            'budget_program' => 20000000,
            'sisa_budget_program' => 5000000,
            'deskripsi' => 'Expense Direct Selling',
            'status' => 'Disetujui',
        ]);

        DirectSalesExpense::create([
            'cluster' => $testCluster,
            'program_name' => 'Grebek POI / Site',
            'tanggal' => date('Y-m-d'),
            'waktu' => date('H:i:s'),
            'nominal_pengeluaran' => 2000000,
            'budget_program' => 10000000,
            'sisa_budget_program' => 8000000,
            'deskripsi' => 'Expense Grebek POI',
            'status' => 'Disetujui',
        ]);

        $cardsDef = [
            [
                'key' => 'ds',
                'title' => 'Direct Selling',
                'col' => 'direct_selling_budget',
                'icon_html' => '<i></i>',
                'bg_icon' => 'bg-red-500',
            ],
            [
                'key' => 'gp',
                'title' => 'Grebek POI / Site',
                'col' => 'grebek_poi_budget',
                'icon_html' => '<i></i>',
                'bg_icon' => 'bg-amber-500',
            ],
        ];

        // Sorted by tertinggi
        $cardsHighest = $this->calculatorService->calculateProgramCards(
            DirectSalesAllocation::class,
            DirectSalesExpense::class,
            $cardsDef,
            $testCluster,
            'tertinggi'
        );

        $this->assertEquals('Direct Selling', $cardsHighest[0]['title']);
        $this->assertEquals(15000000, $cardsHighest[0]['stat']['realisasi']);
        $this->assertEquals('Grebek POI / Site', $cardsHighest[1]['title']);
        $this->assertEquals(2000000, $cardsHighest[1]['stat']['realisasi']);

        // Sorted by terendah
        $cardsLowest = $this->calculatorService->calculateProgramCards(
            DirectSalesAllocation::class,
            DirectSalesExpense::class,
            $cardsDef,
            $testCluster,
            'terendah'
        );

        $this->assertEquals('Grebek POI / Site', $cardsLowest[0]['title']);
        $this->assertEquals('Direct Selling', $cardsLowest[1]['title']);
    }

    /**
     * Test rendering of Indirect Channel page.
     */
    public function test_indirect_channel_index_page_renders_successfully(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get('/budget-bk/indirect-channel');

        $response->assertStatus(200);
        $response->assertSee('TOTAL ALOKASI BUDGET');
        $response->assertSee('TOTAL REALISASI');
        $response->assertSee('SISA BUDGET');
        $response->assertSee('Digital Marketing');
    }

    /**
     * Test rendering of Direct Sales page.
     */
    public function test_direct_sales_index_page_renders_successfully(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get('/budget-bk/direct-sales');

        $response->assertStatus(200);
        $response->assertSee('TOTAL ALOKASI BUDGET');
        $response->assertSee('Direct Selling');
        $response->assertSee('Grebek POI / Site');
    }

    /**
     * Test rendering of Culture Program page.
     */
    public function test_culture_program_index_page_renders_successfully(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get('/budget-bk/culture-program');

        $response->assertStatus(200);
        $response->assertSee('TOTAL ALOKASI BUDGET');
        $response->assertSee('Culture Program');
    }

    /**
     * Test storing expense submission with file upload.
     */
    public function test_store_expense_submission_creates_new_record_and_file(): void
    {
        Storage::fake('public');
        $user = User::first() ?? User::factory()->create();

        $file = UploadedFile::fake()->create('invoice_test.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post('/budget-bk/indirect-channel/expense', [
            'cluster' => 'BALI BARAT',
            'mitra' => 'PT AKAR DAYA',
            'program_name' => 'Digital Marketing',
            'tanggal' => date('Y-m-d'),
            'waktu' => date('H:i:s'),
            'deskripsi' => 'Pengajuan sampel iklan instagram',
            'budget_program' => 10000000,
            'nominal_pengeluaran' => 2500000,
            'sisa_budget_program' => 7500000,
            'evidence' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('indirect_channel_expenses', [
            'cluster' => 'BALI BARAT',
            'program_name' => 'Digital Marketing',
            'nominal_pengeluaran' => 2500000,
            'status' => 'Menunggu',
        ]);
    }
}
