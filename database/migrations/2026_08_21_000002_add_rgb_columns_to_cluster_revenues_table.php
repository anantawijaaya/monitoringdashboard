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
        Schema::table('cluster_revenues', function (Blueprint $table) {
            if (!Schema::hasColumn('cluster_revenues', 'target_rgb')) {
                $table->decimal('target_rgb', 20, 2)->default(0)->after('ach_redeem');
            }
            if (!Schema::hasColumn('cluster_revenues', 'mtd_rgb')) {
                $table->decimal('mtd_rgb', 20, 2)->default(0)->after('target_rgb');
            }
            if (!Schema::hasColumn('cluster_revenues', 'ach_rgb')) {
                $table->double('ach_rgb')->default(0)->after('mtd_rgb');
            }
            if (!Schema::hasColumn('cluster_revenues', 'revenue_rgb')) {
                $table->decimal('revenue_rgb', 20, 2)->default(0)->after('revenue_redeem_pv');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cluster_revenues', function (Blueprint $table) {
            $columns = ['target_rgb', 'mtd_rgb', 'ach_rgb', 'revenue_rgb'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('cluster_revenues', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
