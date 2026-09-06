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
        Schema::create('hierarchy_outlets', function (Blueprint $table) {
            $table->id();
            $table->string('kabupaten');
            $table->string('cluster');
            $table->string('mitra');
            $table->string('branch');
            $table->string('jumlah_outlet');
            $table->string('manager_branch');
            $table->string('group_class')->nullable()->default('bg-white');
            $table->string('badge_color')->nullable()->default('bg-gray-100 text-gray-800');
            $table->timestamps();

            $table->index('branch');
            $table->index('cluster');
            $table->index('kabupaten');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hierarchy_outlets');
    }
};
