<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('peringkat_data');

        Schema::create('peringkat_data', function (Blueprint $table) {
            $table->id();
            $table->string('cluster')->default('BALI BARAT');
            $table->string('city')->default('BULELENG');
            $table->string('period_month')->default('Agustus 2026');
            $table->integer('period_year')->default(2026);

            // Raw Input Columns from Excel Screenshot
            $table->double('target_rev_all', 18, 2)->default(0);
            $table->double('actual_rev_all', 18, 2)->default(0);
            $table->double('target_rev_bb', 18, 2)->default(0);
            $table->double('actual_rev_bb', 18, 2)->default(0);
            $table->double('target_rev_pv', 18, 2)->default(0);
            $table->double('actual_rev_pv', 18, 2)->default(0);
            $table->double('target_rgb', 18, 2)->default(0);
            $table->double('actual_rgb', 18, 2)->default(0);
            $table->double('omzet_rev_m1', 18, 2)->default(0);
            $table->double('mtd_m1', 18, 2)->default(0);
            $table->double('mtd', 18, 2)->default(0);
            $table->double('outlet_pjp', 18, 2)->default(0);
            $table->double('outlet_pjp_growth', 8, 2)->default(0);

            // Computed Performance Metrics & Ranking Scores
            $table->double('ach_revenue_all', 8, 2)->default(0);
            $table->double('ach_broadband', 8, 2)->default(0);
            $table->double('ach_redeem', 8, 2)->default(0);
            $table->double('ach_rgb', 8, 2)->default(0);
            $table->double('growth_mom', 8, 2)->default(0);
            $table->double('total_score', 18, 2)->default(0);

            // Status & Notes
            $table->string('status')->default('Optimal');
            $table->text('notes')->nullable();

            $table->timestamps();
        });

        // Copy existing data from revenue_data table if available
        if (Schema::hasTable('revenue_data')) {
            $existing = DB::table('revenue_data')->get();
            foreach ($existing as $item) {
                $city = $item->kabupaten ?? 'BULELENG';
                $cluster = $item->cluster_name ?? 'BALI BARAT';
                $targetRevAll = $item->target_revenue_all ?? 0;
                $actualRevAll = $item->mtd_revenue_all ?? 0;
                $targetRevBb = $item->target_broadband ?? 0;
                $actualRevBb = $item->mtd_broadband ?? 0;
                $targetRevPv = $item->target_redeem ?? 0;
                $actualRevPv = $item->mtd_redeem ?? 0;
                $targetRgb = $item->target_rgb ?? 0;
                $actualRgb = $item->mtd_rgb ?? 0;
                $omzetRevM1 = $item->revenue_last_month ?? 0;
                $mtdM1 = $item->revenue_last_month ?? 0;
                $mtd = $item->mtd_revenue_all ?? 0;
                $growthMom = floatval($item->growth_mom ?? 0);
                $totalScore = floatval($actualRevAll) + floatval($actualRevBb) + floatval($actualRevPv) + floatval($actualRgb) + $growthMom;

                DB::table('peringkat_data')->insert([
                    'cluster' => $cluster,
                    'city' => $city,
                    'period_month' => $item->period_month ?? 'Agustus 2026',
                    'period_year' => $item->period_year ?? 2026,
                    'target_rev_all' => $targetRevAll,
                    'actual_rev_all' => $actualRevAll,
                    'target_rev_bb' => $targetRevBb,
                    'actual_rev_bb' => $actualRevBb,
                    'target_rev_pv' => $targetRevPv,
                    'actual_rev_pv' => $actualRevPv,
                    'target_rgb' => $targetRgb,
                    'actual_rgb' => $actualRgb,
                    'omzet_rev_m1' => $omzetRevM1,
                    'mtd_m1' => $mtdM1,
                    'mtd' => $mtd,
                    'outlet_pjp' => 0,
                    'outlet_pjp_growth' => 0,
                    'ach_revenue_all' => $item->ach_revenue_all ?? 0,
                    'ach_broadband' => $item->ach_broadband ?? 0,
                    'ach_redeem' => $item->ach_redeem ?? 0,
                    'ach_rgb' => $item->ach_rgb ?? 0,
                    'growth_mom' => $growthMom,
                    'total_score' => $totalScore,
                    'status' => $item->status ?? 'Optimal',
                    'notes' => $item->notes ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peringkat_data');
    }
};
