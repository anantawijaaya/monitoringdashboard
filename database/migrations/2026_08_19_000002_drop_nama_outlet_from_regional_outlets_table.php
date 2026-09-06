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
        Schema::table('regional_outlets', function (Blueprint $table) {
            if (Schema::hasColumn('regional_outlets', 'nama_outlet')) {
                $table->dropColumn('nama_outlet');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regional_outlets', function (Blueprint $table) {
            $table->string('nama_outlet')->nullable()->after('id_outlet');
        });
    }
};
