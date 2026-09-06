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
        Schema::dropIfExists('cluster_revenues');
        
        Schema::create('cluster_revenues', function (Blueprint $table) {
            $table->id();
            $table->string('cluster_name');
            $table->string('kabupaten', 255)->nullable();
            $table->string('branch')->nullable();
            $table->string('period_month', 50)->default('Agustus 2026');
            $table->integer('period_year')->default(2026);

            // 1. REVENUE ALL (TARGET, MTD, ACH %)
            $table->decimal('target_revenue_all', 20, 2)->default(0);
            $table->decimal('mtd_revenue_all', 20, 2)->default(0);
            $table->double('ach_revenue_all')->default(0);

            // 2. REVENUE BROADBAND (TARGET, MTD, ACH %)
            $table->decimal('target_broadband', 20, 2)->default(0);
            $table->decimal('mtd_broadband', 20, 2)->default(0);
            $table->double('ach_broadband')->default(0);

            // 3. REVENUE REDEEM PV (TARGET, MTD, ACH %)
            $table->decimal('target_redeem', 20, 2)->default(0);
            $table->decimal('mtd_redeem', 20, 2)->default(0);
            $table->double('ach_redeem')->default(0);

            // 4. GROWTH REVENUE (DATA BLN SEBELUMNYA, DATA BULAN SEKARANG, MoM %)
            $table->decimal('revenue_last_month', 20, 2)->default(0);
            $table->decimal('revenue_current_month', 20, 2)->default(0);
            $table->double('growth_mom')->default(0);

            // Compatibility aliases & metadata
            $table->decimal('revenue_all', 20, 2)->default(0);
            $table->decimal('revenue_broadband', 20, 2)->default(0);
            $table->decimal('revenue_redeem_pv', 20, 2)->default(0);
            $table->decimal('target_revenue', 20, 2)->default(0);
            $table->double('achievement_rate')->default(0);

            $table->string('status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['period_year', 'period_month']);
            $table->index('cluster_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cluster_revenues');
    }
};
