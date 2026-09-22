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
        Schema::create('indirect_channel_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('cluster')->nullable();
            $table->string('mitra')->nullable();
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->decimal('digital_marketing_budget', 15, 2)->default(0);
            $table->decimal('cvm_program_budget', 15, 2)->default(0);
            $table->decimal('engagement_outlet_budget', 15, 2)->default(0);
            $table->decimal('branding_outlet_budget', 15, 2)->default(0);
            $table->decimal('program_sales_outlet_budget', 15, 2)->default(0);
            $table->decimal('voucher_games_budget', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('indirect_channel_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('program_name');
            $table->date('tanggal');
            $table->time('waktu');
            $table->text('deskripsi');
            $table->decimal('budget_program', 15, 2)->default(0);
            $table->decimal('nominal_pengeluaran', 15, 2)->default(0);
            $table->decimal('sisa_budget_program', 15, 2)->default(0);
            $table->string('evidence_path')->nullable();
            $table->string('evidence_original_name')->nullable();
            $table->string('status')->default('Menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indirect_channel_expenses');
        Schema::dropIfExists('indirect_channel_allocations');
    }
};
