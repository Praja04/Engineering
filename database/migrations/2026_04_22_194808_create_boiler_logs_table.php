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
        Schema::create('boiler_logs', function (Blueprint $table) {
            $table->id();

            // Waktu (unik per jam)
            $table->dateTime('waktu')->unique();

            // Steam & Pressure
            $table->decimal('PVSteam', 8, 2)->nullable();
            $table->decimal('FeedPressure', 8, 2)->nullable();
            $table->decimal('Press_Pasteur', 8, 2)->nullable();

            // Water
            $table->decimal('LevelFeedWater', 8, 2)->nullable();
            $table->decimal('InletWaterFlow', 10, 2)->nullable();
            $table->decimal('OutletSteamFlow', 10, 2)->nullable();
            $table->decimal('SuhuFeedTank', 8, 2)->nullable();

            // Fan
            $table->decimal('IDFan', 8, 2)->nullable();
            $table->decimal('LHFDFan', 8, 2)->nullable();
            $table->decimal('RHFDFan', 8, 2)->nullable();

            // Stoker
            $table->decimal('LHStoker', 8, 2)->nullable();
            $table->decimal('RHStoker', 8, 2)->nullable();

            // Temperature
            $table->decimal('LHTemp', 8, 2)->nullable();
            $table->decimal('RHTemp', 8, 2)->nullable();

            // Gas
            $table->decimal('O2', 5, 2)->nullable();
            $table->decimal('CO2', 5, 2)->nullable();

            // Guillotine
            $table->decimal('LHGuiloutine', 8, 2)->nullable();
            $table->decimal('RHGuiloutine', 8, 2)->nullable();

            // Additional Sensor Data Columns
            $table->decimal('WaterPump1', 8, 2)->nullable();
            $table->decimal('WaterPump2', 8, 2)->nullable();
            $table->decimal('Batubara_FK', 10, 3)->nullable();
            $table->decimal('Steam_FK', 10, 3)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boiler_logs');
    }
};
