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
        Schema::table('hierarchy_outlets', function (Blueprint $table) {
            if (Schema::hasColumn('hierarchy_outlets', 'raw_outlet')) {
                $table->dropColumn('raw_outlet');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hierarchy_outlets', function (Blueprint $table) {
            $table->integer('raw_outlet')->default(0)->after('jumlah_outlet');
        });
    }
};
