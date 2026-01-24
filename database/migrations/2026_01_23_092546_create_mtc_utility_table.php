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
        Schema::create('mtc_utility', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mesin');
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('paket')->nullable();

            // Cooling Power
            $table->boolean('cleaning_saringan_cooling_tower')->nullable();
            $table->boolean('cleaning_unit_cooling_tower')->nullable();
            $table->boolean('cleaning_bak_cooling_tower')->nullable();

            // RO
            $table->boolean('check_sensor_tank_farm_ro_produk')->nullable();
            $table->boolean('cleaning_flow_rate_mmf_1')->nullable();
            $table->boolean('cleaning_flow_rate_mmf_2')->nullable();
            $table->boolean('cleaning_flow_rate_ro_produk')->nullable();
            $table->boolean('cleaning_flow_rate_ro_reject')->nullable();
            $table->boolean('penggantian_micron_filter_cip')->nullable();
            $table->boolean('penggantian_micron_filter_makeup_water')->nullable();
            $table->boolean('cleaning_cip_tank')->nullable();
            $table->boolean('cip_membrane_reverse_osmosis')->nullable();
            $table->boolean('check_fungsi_valve')->nullable();
            $table->boolean('cleaning_unit_ro_mesin')->nullable();

            // Compressor
            $table->boolean('sirkulasi_phe_aq55vsd')->nullable();
            $table->boolean('penggantian_air_ro_aq55vsd')->nullable();
            $table->boolean('cleaning_compressor_aq55vsd')->nullable();
            $table->boolean('cleaning_jalur_cooling_aq55vsd')->nullable();
            $table->boolean('cleaning_dryer_fd185')->nullable();
            $table->boolean('cleaning_compressor_ga37')->nullable();
            $table->boolean('cleaning_dryer_fd120')->nullable();
            $table->boolean('lubrikasi_motor_compressor_aq55vsd')->nullable();
            $table->boolean('cleaning_compressor_sm55')->nullable();

            // Tank Farm
            $table->boolean('cleaning_sensor_level_tank_farm')->nullable();
            $table->boolean('cleaning_sensor_level_fresh_water_menara')->nullable();
            $table->boolean('cleaning_sensor_level_ro_reject_menara')->nullable();
            $table->boolean('cleaning_sensor_level_intermediate')->nullable();

            // Boiler
            $table->boolean('check_safety_valve')->nullable();
            $table->boolean('cleaning_level_gauge')->nullable();
            $table->boolean('cleaning_level_transmitter')->nullable();
            $table->boolean('check_pressure_transmitter')->nullable();
            $table->boolean('check_temperature_transmitter')->nullable();
            $table->boolean('cleaning_sensor_o2_co2')->nullable();
            $table->boolean('check_chaingrate')->nullable();
            $table->boolean('check_ruang_bakar')->nullable();
            $table->boolean('check_back_chamber')->nullable();
            $table->boolean('check_guillotine')->nullable();
            $table->boolean('check_wet_ash_conveyor')->nullable();
            $table->boolean('check_bottom_ash_conveyor')->nullable();
            $table->boolean('check_conveyor_batu_bara')->nullable();
            $table->boolean('check_feeder')->nullable();
            $table->boolean('cleaning_bak_wet_ash_conveyor')->nullable();
            $table->boolean('check_feed_tank')->nullable();

            // WWTP
            $table->boolean('check_line_limbah')->nullable();
            $table->boolean('check_line_chemical')->nullable();
            $table->boolean('check_tangki_kotak')->nullable();
            $table->boolean('check_tangki_bulat')->nullable();

            $table->text('keterangan')->nullable();
            $table->string('korektif')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_utility');
    }
};
