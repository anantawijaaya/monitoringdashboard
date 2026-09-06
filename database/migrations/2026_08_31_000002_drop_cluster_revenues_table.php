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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate if rolled back
        if (!Schema::hasTable('cluster_revenues')) {
            Schema::create('cluster_revenues', function (Blueprint $table) {
                $table->id();
                $table->string('cluster_name')->default('BALI BARAT');
                $table->string('kabupaten')->default('BULELENG');
                $table->string('branch')->nullable();
                $table->string('period_month')->default('JULI');
                $table->integer('period_year')->default(2026);
                $table->double('target_revenue_all')->default(0);
                $table->double('mtd_revenue_all')->default(0);
                $table->double('ach_revenue_all')->default(0);
                $table->double('target_broadband')->default(0);
                $table->double('mtd_broadband')->default(0);
                $table->double('ach_broadband')->default(0);
                $table->double('target_redeem')->default(0);
                $table->double('mtd_redeem')->default(0);
                $table->double('ach_redeem')->default(0);
                $table->double('target_rgb')->default(0);
                $table->double('mtd_rgb')->default(0);
                $table->double('ach_rgb')->default(0);
                $table->double('revenue_last_month')->default(0);
                $table->double('revenue_current_month')->default(0);
                $table->double('growth_mom')->default(0);
                $table->string('status')->default('Optimal');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }
};
