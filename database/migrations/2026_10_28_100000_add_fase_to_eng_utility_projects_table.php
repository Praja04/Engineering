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
        Schema::table('eng_utility_projects', function (Blueprint $table) {
            $table->string('fase', 50)->nullable()->default('Fase 1')->after('priority')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eng_utility_projects', function (Blueprint $table) {
            $table->dropColumn('fase');
        });
    }
};
