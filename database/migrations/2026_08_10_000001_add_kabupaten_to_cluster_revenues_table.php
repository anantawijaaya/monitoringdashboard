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
        Schema::table('cluster_revenues', function (Blueprint $table) {
            if (!Schema::hasColumn('cluster_revenues', 'kabupaten')) {
                $table->string('kabupaten', 255)->nullable()->after('cluster_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cluster_revenues', function (Blueprint $table) {
            if (Schema::hasColumn('cluster_revenues', 'kabupaten')) {
                $table->dropColumn('kabupaten');
            }
        });
    }
};
