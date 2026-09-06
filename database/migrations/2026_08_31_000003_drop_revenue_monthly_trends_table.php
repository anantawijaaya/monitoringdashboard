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
        Schema::dropIfExists('revenue_monthly_trends');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('revenue_monthly_trends')) {
            Schema::create('revenue_monthly_trends', function (Blueprint $table) {
                $table->id();
                $table->integer('year')->default(2026);
                $table->integer('month_number');
                $table->string('month_name', 10);
                $table->decimal('growth_revenue_all', 8, 2)->default(0);
                $table->decimal('growth_broadband', 8, 2)->default(0);
                $table->decimal('growth_redeem_pv', 8, 2)->default(0);
                $table->decimal('value_revenue_all', 15, 2)->default(0);
                $table->decimal('value_broadband', 15, 2)->default(0);
                $table->decimal('value_redeem_pv', 15, 2)->default(0);
                $table->decimal('target_kpi_growth', 8, 2)->default(2.00);
                $table->decimal('target_revenue_value', 15, 2)->default(0);
                $table->timestamps();

                $table->unique(['year', 'month_number']);
            });
        }
    }
};
