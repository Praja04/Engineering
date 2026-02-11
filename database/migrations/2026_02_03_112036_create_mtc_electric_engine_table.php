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
        Schema::create('mtc_electric_engine_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mtc_master_mesin')->onDelete('restrict');
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->onDelete('cascade');

            // Forklift Electrical - General
            $table->boolean('check_buzzer_back')->nullable();
            $table->boolean('check_klakson')->nullable();
            $table->boolean('check_pilot_lamp')->nullable();
            $table->boolean('check_lampu_sorot')->nullable();
            $table->boolean('check_lampu_kombinasi_kanan_belakang')->nullable();
            $table->boolean('check_lampu_kombinasi_kiri_belakang')->nullable();
            $table->boolean('check_kaca_sepion')->nullable();

            // Battery, Charger & Electrical System
            $table->boolean('check_battery')->nullable();
            $table->boolean('check_skun_battery')->nullable();
            $table->boolean('check_terminal_charger_battery')->nullable();
            $table->boolean('check_kunci_kontak')->nullable();
            $table->boolean('check_main_contactor')->nullable();
            $table->boolean('check_microswitch')->nullable();
            $table->boolean('check_eps_controller')->nullable();
            $table->boolean('check_steering_motor')->nullable();
            $table->boolean('check_fan')->nullable();
            $table->boolean('check_fuse')->nullable();
            $table->boolean('check_display_control')->nullable();
            $table->boolean('check_wiring_terminal')->nullable();
            $table->boolean('check_carbon_brush')->nullable();

            // Drive, Steering, Mast, Hydraulic & Braking System
            $table->boolean('check_steering_wheel')->nullable();
            $table->boolean('check_baut_roda')->nullable();
            $table->boolean('check_drive_caster_load_wheel')->nullable();
            $table->boolean('check_lift_chain')->nullable();
            $table->boolean('check_lift_bracket')->nullable();
            $table->boolean('check_hydraulic_hose')->nullable();
            $table->boolean('check_motor_hydraulic_pump')->nullable();
            $table->boolean('check_fork')->nullable();
            $table->boolean('check_lift_rollers')->nullable();
            $table->boolean('check_mast_rollers')->nullable();
            $table->boolean('check_lift_cylinders')->nullable();
            $table->boolean('check_tilt_cylinders')->nullable();
            $table->boolean('check_control_valve')->nullable();
            $table->boolean('check_hydraulic_tank')->nullable();
            $table->boolean('check_overhead_guard')->nullable();
            $table->boolean('check_all_bolt_nut')->nullable();
            $table->boolean('check_power_steering')->nullable();
            $table->boolean('check_brake_cam_adjust_bolt')->nullable();
            $table->boolean('check_axle')->nullable();
            $table->boolean('check_greasing_point')->nullable();
            $table->boolean('check_air_spring')->nullable();

            // oil
            $table->boolean('ganti_gear_oil')->nullable();
            $table->boolean('ganti_hydraulic_oil')->nullable();
            $table->boolean('ganti_return_filter')->nullable();
            $table->boolean('ganti_brake_oil')->nullable();

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
        Schema::dropIfExists('mtc_electric_engine_inspections');
    }
};
