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
        Schema::table('peringkat_data', function (Blueprint $table) {
            if (!Schema::hasColumn('peringkat_data', 'type')) {
                $table->string('type')->default('HIGH')->after('period_year');
            }
            if (!Schema::hasColumn('peringkat_data', 'tgt_3_percent')) {
                $table->double('tgt_3_percent', 18, 2)->default(0)->after('mtd');
            }
            if (!Schema::hasColumn('peringkat_data', 'growth')) {
                $table->double('growth', 8, 2)->default(0)->after('tgt_3_percent');
            }
            if (!Schema::hasColumn('peringkat_data', 'growth_tgt')) {
                $table->double('growth_tgt', 8, 2)->default(3.0)->after('growth');
            }
            if (!Schema::hasColumn('peringkat_data', 'rev_growth_ach_percent')) {
                $table->double('rev_growth_ach_percent', 8, 2)->default(0)->after('growth_tgt');
            }
            if (!Schema::hasColumn('peringkat_data', 'ratio_outlet_pjp')) {
                $table->double('ratio_outlet_pjp', 8, 2)->default(0)->after('outlet_pjp_growth');
            }
            if (!Schema::hasColumn('peringkat_data', 'omzet_outlet_ach')) {
                $table->double('omzet_outlet_ach', 8, 2)->default(0)->after('ratio_outlet_pjp');
            }
            if (!Schema::hasColumn('peringkat_data', 'ach_rev_all')) {
                $table->double('ach_rev_all', 8, 2)->default(0)->after('ach_revenue_all');
            }
            if (!Schema::hasColumn('peringkat_data', 'ach_rev_bb')) {
                $table->double('ach_rev_bb', 8, 2)->default(0)->after('ach_broadband');
            }
            if (!Schema::hasColumn('peringkat_data', 'ach_rev_pv')) {
                $table->double('ach_rev_pv', 8, 2)->default(0)->after('ach_redeem');
            }
            if (!Schema::hasColumn('peringkat_data', 'score_rev_all')) {
                $table->double('score_rev_all', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('peringkat_data', 'score_rev_bb')) {
                $table->double('score_rev_bb', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('peringkat_data', 'score_rev_pv')) {
                $table->double('score_rev_pv', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('peringkat_data', 'score_rgb')) {
                $table->double('score_rgb', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('peringkat_data', 'score_omzet')) {
                $table->double('score_omzet', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('peringkat_data', 'weight_rev_all')) {
                $table->double('weight_rev_all', 5, 2)->default(15.0);
            }
            if (!Schema::hasColumn('peringkat_data', 'weight_rev_bb')) {
                $table->double('weight_rev_bb', 5, 2)->default(15.0);
            }
            if (!Schema::hasColumn('peringkat_data', 'weight_rev_pv')) {
                $table->double('weight_rev_pv', 5, 2)->default(25.0);
            }
            if (!Schema::hasColumn('peringkat_data', 'weight_rgb')) {
                $table->double('weight_rgb', 5, 2)->default(20.0);
            }
            if (!Schema::hasColumn('peringkat_data', 'weight_omzet')) {
                $table->double('weight_omzet', 5, 2)->default(25.0);
            }
            if (!Schema::hasColumn('peringkat_data', 'final_score_rev_all')) {
                $table->double('final_score_rev_all', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('peringkat_data', 'final_score_rev_bb')) {
                $table->double('final_score_rev_bb', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('peringkat_data', 'final_score_rev_pv')) {
                $table->double('final_score_rev_pv', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('peringkat_data', 'final_score_rgb')) {
                $table->double('final_score_rgb', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('peringkat_data', 'final_score_omzet')) {
                $table->double('final_score_omzet', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('peringkat_data', 'final_score')) {
                $table->double('final_score', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('peringkat_data', 'class')) {
                $table->string('class')->default('BRONZE');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peringkat_data', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'tgt_3_percent',
                'growth',
                'growth_tgt',
                'rev_growth_ach_percent',
                'ratio_outlet_pjp',
                'omzet_outlet_ach',
                'ach_rev_all',
                'ach_rev_bb',
                'ach_rev_pv',
                'score_rev_all',
                'score_rev_bb',
                'score_rev_pv',
                'score_rgb',
                'score_omzet',
                'weight_rev_all',
                'weight_rev_bb',
                'weight_rev_pv',
                'weight_rgb',
                'weight_omzet',
                'final_score_rev_all',
                'final_score_rev_bb',
                'final_score_rev_pv',
                'final_score_rgb',
                'final_score_omzet',
                'final_score',
                'class'
            ]);
        });
    }
};
