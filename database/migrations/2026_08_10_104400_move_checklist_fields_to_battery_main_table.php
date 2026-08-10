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
        Schema::table('mtc_battery_inspections', function (Blueprint $table) {
            $table->dropColumn(['intercell', 'kondisi_skun', 'kondisi_unit']);
        });

        Schema::table('mtc_battery_main', function (Blueprint $table) {
            $table->boolean('intercell')->nullable()->after('grounding');
            $table->boolean('kondisi_skun')->nullable()->after('intercell');
            $table->boolean('kondisi_unit')->nullable()->after('kondisi_skun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mtc_battery_main', function (Blueprint $table) {
            $table->dropColumn(['intercell', 'kondisi_skun', 'kondisi_unit']);
        });

        Schema::table('mtc_battery_inspections', function (Blueprint $table) {
            $table->boolean('intercell')->nullable()->after('level_air_aki');
            $table->boolean('kondisi_skun')->nullable()->after('intercell');
            $table->boolean('kondisi_unit')->nullable()->after('kondisi_skun');
        });
    }
};
