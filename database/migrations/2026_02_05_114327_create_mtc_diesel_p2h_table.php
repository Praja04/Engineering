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
        Schema::create('mtc_diesel_p2h_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mtc_master_mesin')->onDelete('restrict');
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->onDelete('cascade');
            $table->integer('shift');
            $table->string('no_unit');
            $table->text('catatan')->nullable();

            $table->boolean('klakson')->nullable();
            $table->boolean('buzzer_back')->nullable();
            $table->boolean('oli_mesin')->nullable();
            $table->boolean('radiator_hose')->nullable();
            $table->boolean('water_pump')->nullable();
            $table->boolean('injection_system')->nullable();
            $table->boolean('fan_vbelt')->nullable();
            $table->boolean('turbocharger_manifold')->nullable();
            $table->boolean('tensioner_belt')->nullable();
            $table->boolean('starting_motor')->nullable();
            $table->boolean('alternator')->nullable();
            $table->boolean('control_display')->nullable();
            $table->boolean('oli_transmisi')->nullable();
            $table->boolean('aki')->nullable();
            $table->boolean('engine_mounting')->nullable();
            $table->boolean('filter_oli_transmisi')->nullable();
            $table->boolean('fungsi_rem')->nullable();
            $table->boolean('fungsi_kopling')->nullable();
            $table->boolean('oli_hydraulic')->nullable();
            $table->boolean('hydraulic_system')->nullable();
            $table->boolean('steering_system')->nullable();
            $table->boolean('body_back_rest')->nullable();
            $table->boolean('kaca_spion')->nullable();
            $table->boolean('bucket_pin')->nullable();
            $table->boolean('dump_pin_bushing')->nullable();
            $table->boolean('seal_hydraulic')->nullable();
            $table->boolean('roda_ban_baut')->nullable();
            $table->boolean('lampu_unit')->nullable();
            $table->boolean('baut_bearing_molen')->nullable();
            $table->boolean('baut_hanger_as')->nullable();
            $table->boolean('baut_grease')->nullable();
            $table->boolean('katup_pembuangan_angin')->nullable();
            $table->string('hours_meter')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_diesel_p2h_inspections');
    }
};
