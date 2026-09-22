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
        if (!Schema::hasTable('direct_sales_allocations')) {
            Schema::create('direct_sales_allocations', function (Blueprint $table) {
                $table->id();
                $table->string('cluster')->default('General');
                $table->string('mitra')->default('All Mitra');
                $table->decimal('total_budget', 15, 2)->default(0);
                $table->decimal('direct_selling_budget', 15, 2)->default(0);
                $table->decimal('grebek_poi_budget', 15, 2)->default(0);
                $table->decimal('dls_skulid_budget', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('direct_sales_expenses')) {
            Schema::create('direct_sales_expenses', function (Blueprint $table) {
                $table->id();
                $table->string('cluster')->default('General');
                $table->string('mitra')->default('All Mitra');
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_sales_expenses');
        Schema::dropIfExists('direct_sales_allocations');
    }
};
