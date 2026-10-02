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
        $mitraMap = [
            'BALI BARAT' => 'PT. CAHAYA GEMILANG CELLULAR',
            'BALI TENGAH' => 'PT. SOLUSINDO KREASI JAYATECH',
            'BALI TIMUR' => 'PT. CAHAYA GEMILANG CELLULAR',
            'ENDE SIKKA' => 'CV. RAJAWALI CELLULAR INDONESIA',
            'FLORES TIMUR' => 'CV. RAJAWALI CELLULAR INDONESIA',
            'MANGGARAI' => 'CV. RAJAWALI CELLULAR INDONESIA',
            'KUPANG ROTE' => 'PT. KINARYA SELARAS SOLUSI',
            'MALAKA TIMTIM B' => 'PT. NARINDO SOLUSI TELEKOMUNIKASI',
            'MALAKA TIMTIM BELU' => 'PT. NARINDO SOLUSI TELEKOMUNIKASI',
            'SUMBA' => 'CV. RAJAWALI CELLULAR INDONESIA',
            'LOMBOK' => 'PT. AKAR DAYA',
            'SUMBAWA BARAT' => 'PT. MITRA CIPTA TEKNOLOGI',
            'SUMBAWA TIMUR' => 'PT. MITRA CIPTA TEKNOLOGI',
        ];

        $tables = [
            'indirect_channel_allocations',
            'indirect_channel_expenses',
            'direct_sales_allocations',
            'direct_sales_expenses',
            'culture_program_allocations',
            'culture_program_expenses',
        ];

        foreach ($tables as $table) {
            foreach ($mitraMap as $cluster => $newMitra) {
                \Illuminate\Support\Facades\DB::table($table)->where('cluster', $cluster)->update(['mitra' => $newMitra]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversing data migration to avoid complex undo logic
    }
};
