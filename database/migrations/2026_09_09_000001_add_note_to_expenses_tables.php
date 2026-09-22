<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('indirect_channel_expenses') && !Schema::hasColumn('indirect_channel_expenses', 'note')) {
            Schema::table('indirect_channel_expenses', function (Blueprint $table) {
                $table->text('note')->nullable();
            });
        }
        if (Schema::hasTable('direct_sales_expenses') && !Schema::hasColumn('direct_sales_expenses', 'note')) {
            Schema::table('direct_sales_expenses', function (Blueprint $table) {
                $table->text('note')->nullable();
            });
        }
        if (Schema::hasTable('culture_program_expenses') && !Schema::hasColumn('culture_program_expenses', 'note')) {
            Schema::table('culture_program_expenses', function (Blueprint $table) {
                $table->text('note')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('indirect_channel_expenses') && Schema::hasColumn('indirect_channel_expenses', 'note')) {
            Schema::table('indirect_channel_expenses', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
        if (Schema::hasTable('direct_sales_expenses') && Schema::hasColumn('direct_sales_expenses', 'note')) {
            Schema::table('direct_sales_expenses', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
        if (Schema::hasTable('culture_program_expenses') && Schema::hasColumn('culture_program_expenses', 'note')) {
            Schema::table('culture_program_expenses', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
    }
};
