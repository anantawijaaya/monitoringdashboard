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
        Schema::table('indirect_channel_expenses', function (Blueprint $table) {
            $table->string('cluster')->nullable()->after('id');
            $table->string('mitra')->nullable()->after('cluster');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indirect_channel_expenses', function (Blueprint $table) {
            $table->dropColumn(['cluster', 'mitra']);
        });
    }
};
