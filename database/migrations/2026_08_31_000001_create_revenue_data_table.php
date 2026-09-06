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
        Schema::create('revenue_data', function (Blueprint $table) {
            $table->id();
            $table->string('cluster_name')->default('BALI BARAT');
            $table->string('kabupaten')->default('BULELENG');
            $table->string('period_month')->default('JULI');
            $table->integer('period_year')->default(2026);

            // Revenue All (TARGET REV ALL, MTD REV ALL)
            $table->double('target_revenue_all')->default(0);
            $table->double('mtd_revenue_all')->default(0);
            $table->double('ach_revenue_all')->default(0);

            // Revenue Broadband (TARGET BB, MTD BB)
            $table->double('target_broadband')->default(0);
            $table->double('mtd_broadband')->default(0);
            $table->double('ach_broadband')->default(0);

            // Revenue Redeem PV (TARGET PV, MTD PV)
            $table->double('target_redeem')->default(0);
            $table->double('mtd_redeem')->default(0);
            $table->double('ach_redeem')->default(0);

            // Revenue RGB (Target RGB All, MTD RGB All)
            $table->double('target_rgb')->default(0);
            $table->double('mtd_rgb')->default(0);
            $table->double('ach_rgb')->default(0);

            // Growth Revenue MoM (Data Bln Sebelumnya, Data Bln Sekarang)
            $table->double('revenue_last_month')->default(0);
            $table->double('revenue_current_month')->default(0);
            $table->double('growth_mom')->default(0);

            // Legacy Alias Columns
            $table->double('revenue_all')->default(0);
            $table->double('revenue_broadband')->default(0);
            $table->double('revenue_redeem_pv')->default(0);
            $table->double('revenue_rgb')->default(0);
            $table->double('target_revenue')->default(0);
            $table->double('achievement_rate')->default(0);

            // Status & Notes
            $table->string('status')->default('Optimal');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_data');
    }
};
