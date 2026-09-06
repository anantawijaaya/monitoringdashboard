<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('kelas_kpis');

        Schema::create('kelas_kpis', function (Blueprint $table) {
            $table->id();
            $table->string('region')->default('BALI NUSRA');
            $table->string('cluster');
            $table->string('new_cluster')->nullable();
            $table->string('periode')->nullable()->default('2026-08');
            $table->string('type')->default('HIGH'); // VERY HIGH, HIGH, LOW
            
            // Image KPI Columns
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
            $table->double('tgt_3_percent', 18, 2)->default(0);
            $table->double('mtd', 18, 2)->default(0);
            $table->double('growth', 8, 2)->default(0);
            $table->double('growth_tgt', 8, 2)->default(3.0);
            $table->double('rev_growth_ach_percent', 8, 2)->default(0);
            $table->double('outlet_pjp', 18, 2)->default(0);
            $table->double('outlet_pjp_growth', 8, 2)->default(0);
            $table->double('ratio_outlet_pjp', 8, 2)->default(0);
            $table->double('omzet_outlet_ach', 8, 2)->default(0);
            $table->double('weight', 5, 2)->default(15.0);
            $table->double('omzet_outlet_ach_score', 8, 2)->default(0);
            $table->double('final_score', 8, 2)->default(0);

            // Category Breakdown Metrics & Scores
            $table->double('ach_rev_all', 8, 2)->default(0);
            $table->double('ach_rev_bb', 8, 2)->default(0);
            $table->double('ach_rev_pv', 8, 2)->default(0);
            $table->double('ach_rgb', 8, 2)->default(0);

            $table->double('score_rev_all', 5, 2)->default(0);
            $table->double('score_rev_bb', 5, 2)->default(0);
            $table->double('score_rev_pv', 5, 2)->default(0);
            $table->double('score_rgb', 5, 2)->default(0);
            $table->double('score_omzet', 5, 2)->default(0);

            $table->double('weight_rev_all', 5, 2)->default(15.0);
            $table->double('weight_rev_bb', 5, 2)->default(15.0);
            $table->double('weight_rev_pv', 5, 2)->default(25.0);
            $table->double('weight_rgb', 5, 2)->default(20.0);
            $table->double('weight_omzet', 5, 2)->default(25.0);

            $table->double('final_score_rev_all', 5, 2)->default(0);
            $table->double('final_score_rev_bb', 5, 2)->default(0);
            $table->double('final_score_rev_pv', 5, 2)->default(0);
            $table->double('final_score_rgb', 5, 2)->default(0);
            $table->double('final_score_omzet', 5, 2)->default(0);

            // Legacy Metrics
            $table->double('revenue_all', 18, 2)->default(0);
            $table->double('achieved', 18, 2)->default(0);
            $table->double('ach_percent', 8, 2)->default(0);
            $table->double('score', 5, 2)->default(0);
            $table->double('total_score', 5, 2)->default(0);
            $table->string('class')->default('BRONZE'); // GOLD, SILVER, BRONZE

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_kpis');
    }
};
