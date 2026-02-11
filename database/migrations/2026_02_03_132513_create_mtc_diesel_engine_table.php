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
        Schema::create('mtc_diesel_engine_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mtc_master_mesin')->onDelete('restrict');
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->onDelete('cascade');

            // | ENGINE
            $table->boolean('check_kondisi_level_oli_mesin')->nullable();
            $table->boolean('check_kondisi_radiator_hose')->nullable();
            $table->boolean('check_kondisi_level_air_radiator')->nullable();
            $table->boolean('check_water_pump')->nullable();
            $table->boolean('check_injection_pump_injector_piping')->nullable();
            $table->boolean('check_turbocharger_manifold')->nullable();
            $table->boolean('check_fan_v_belt')->nullable();
            $table->boolean('check_automatic_tensioner_belt')->nullable();
            $table->boolean('check_engine_mounting')->nullable();
            $table->boolean('check_air_filter_condition')->nullable();
            $table->boolean('check_clearence_valve_drain_valve')->nullable();
            $table->boolean('check_engine_oil_filter')->nullable();
            $table->boolean('check_air_radiator')->nullable();
            $table->boolean('check_minyak_kopling')->nullable();
            $table->boolean('check_fuel_filter')->nullable();

            // | ELECTRIC
            $table->boolean('check_kondisi_aki_level_air_aki')->nullable();
            $table->boolean('check_fungsi_starting_motor')->nullable();
            $table->boolean('check_fungsi_alternator')->nullable();
            $table->boolean('check_sensor_sensor_gauge')->nullable();
            $table->boolean('check_fuse_control_switch')->nullable();
            $table->boolean('check_control_display')->nullable();
            $table->boolean('check_indicator_wiring')->nullable();

            // | TRANSMISI, BRAKE, DRIVE SHAFT
            $table->boolean('check_kondisi_level_oli_transmisi')->nullable();
            $table->boolean('check_fungsi_transmisi')->nullable();
            $table->boolean('check_filter_oli_transmisi')->nullable();
            $table->boolean('check_fungsi_rem')->nullable();
            $table->boolean('check_oli_tidak_ada_yang_bocor')->nullable();
            $table->boolean('check_kondisi_drive_shaft')->nullable();


            // HYDRAULIC
            $table->boolean('check_kondisi_level_hydraulic_oil')->nullable();
            $table->boolean('check_kondisi_hydraulic_oil_filter')->nullable();
            $table->boolean('check_fungsi_hydraulic_system')->nullable();
            $table->boolean('check_fungsi_steering_system')->nullable();
            $table->boolean('check_kondisi_hydraulic_cylinder')->nullable();
            $table->boolean('check_kondisi_steering_cylinder')->nullable();
            $table->boolean('check_kondisi_axle_oil')->nullable();
            $table->boolean('check_kondisi_baut_roda_hydraulic')->nullable();
            $table->boolean('check_kondisi_bucket_pin_bucket')->nullable();
            $table->boolean('check_kondisi_dump_pin_bushing')->nullable();

            //  GENERAL
            $table->boolean('check_klakson')->nullable();
            $table->boolean('check_buzzer_back')->nullable();
            $table->boolean('check_kondisi_basket_fresh_body')->nullable();
            $table->boolean('check_kaca_sepion')->nullable();
            $table->boolean('check_kondisi_roda_ban')->nullable();
            $table->boolean('check_baut_roda_general')->nullable();
            $table->boolean('check_lampu_depan_kanan')->nullable();
            $table->boolean('check_lampu_depan_kiri')->nullable();
            $table->boolean('check_baut_bearing_molen')->nullable();
            $table->boolean('check_baut_hanger_as_roda')->nullable();

            // $table->text('keterangan')->nullable();
            // $table->string('korektif')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_diesel_engine_inspections');
    }
};
