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
        Schema::table('agenda_ro_ws_details', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
        Schema::table('agenda_cooling_tower_details', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
        Schema::table('agenda_compressor_details', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
        Schema::table('agenda_tank_farm_details', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
        Schema::table('agenda_ahu_details', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda_ro_ws_details', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
        Schema::table('agenda_cooling_tower_details', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
        Schema::table('agenda_compressor_details', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
        Schema::table('agenda_tank_farm_details', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
        Schema::table('agenda_ahu_details', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
