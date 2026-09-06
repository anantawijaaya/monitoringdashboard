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
        Schema::table('revenue_data', function (Blueprint $table) {
            if (!Schema::hasColumn('revenue_data', 'omzet_rev_m1')) {
                $table->double('omzet_rev_m1')->default(0)->after('revenue_last_month');
            }
            if (!Schema::hasColumn('revenue_data', 'mtd_m1')) {
                $table->double('mtd_m1')->default(0)->after('omzet_rev_m1');
            }
            if (!Schema::hasColumn('revenue_data', 'mtd')) {
                $table->double('mtd')->default(0)->after('mtd_m1');
            }
            if (!Schema::hasColumn('revenue_data', 'outlet_pjp')) {
                $table->double('outlet_pjp')->default(0)->after('mtd');
            }
            if (!Schema::hasColumn('revenue_data', 'outlet_pjp_growth')) {
                $table->double('outlet_pjp_growth')->default(0)->after('outlet_pjp');
            }
            if (!Schema::hasColumn('revenue_data', 'total_score')) {
                $table->double('total_score')->default(0)->after('outlet_pjp_growth');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revenue_data', function (Blueprint $table) {
            $table->dropColumn([
                'omzet_rev_m1',
                'mtd_m1',
                'mtd',
                'outlet_pjp',
                'outlet_pjp_growth',
                'total_score'
            ]);
        });
    }
};
