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
        Schema::table('boiler_logs', function (Blueprint $table) {
            $table->double('water_flow_total')->nullable()->after('RHStoker');
            $table->double('water_hmi_flow_rate')->nullable()->after('LevelFeedWater');
            $table->double('water_hmi_total')->nullable()->after('water_hmi_flow_rate');
            $table->double('flue_gass_temp')->nullable()->after('CO2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boiler_logs', function (Blueprint $table) {
            $table->dropColumn(['water_flow_total', 'water_hmi_flow_rate', 'water_hmi_total', 'flue_gass_temp']);
        });
    }
};
