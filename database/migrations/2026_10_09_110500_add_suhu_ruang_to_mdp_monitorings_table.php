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
        Schema::table('mdp_monitorings', function (Blueprint $table) {
            $table->decimal('suhu_ruang_trafo', 10, 2)->nullable()->after('level_oil');
            $table->decimal('suhu_ruang_genset', 10, 2)->nullable()->after('suhu_ruang_trafo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mdp_monitorings', function (Blueprint $table) {
            $table->dropColumn(['suhu_ruang_trafo', 'suhu_ruang_genset']);
        });
    }
};
