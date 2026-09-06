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
        Schema::create('regional_outlets', function (Blueprint $table) {
            $table->id();
            $table->string('id_outlet')->unique()->index();
            $table->string('nama_outlet')->nullable();
            $table->decimal('longitude', 11, 8);
            $table->decimal('latitude', 10, 8);
            $table->string('kabupaten')->index();
            $table->string('cluster')->index();
            $table->string('branch')->index();
            $table->decimal('total_omzet', 18, 2)->default(0);
            $table->decimal('flag_omzet', 8, 2)->default(0); // in percentage e.g. -2.5, 0.0, 1.8, 4.5
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regional_outlets');
    }
};
