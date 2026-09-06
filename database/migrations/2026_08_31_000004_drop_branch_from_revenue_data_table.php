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
        if (Schema::hasColumn('revenue_data', 'branch')) {
            Schema::table('revenue_data', function (Blueprint $table) {
                $table->dropColumn('branch');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('revenue_data', 'branch')) {
            Schema::table('revenue_data', function (Blueprint $table) {
                $table->string('branch')->nullable()->default('Singaraja')->after('kabupaten');
            });
        }
    }
};
