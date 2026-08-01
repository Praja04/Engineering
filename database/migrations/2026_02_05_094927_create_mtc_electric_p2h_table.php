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
        Schema::create('mtc_electric_p2h_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->onDelete('cascade');
            $table->string('no_unit');
            $table->integer('shift');
            $table->text('catatan')->nullable();

            $table->boolean('level_minyak_rem')->nullable();
            $table->boolean('level_oli_hydraulic')->nullable();
            $table->boolean('isi_air_aki')->nullable();
            $table->boolean('baterai')->nullable();
            $table->boolean('hydraulic_system')->nullable();
            $table->boolean('selang_hydraulic')->nullable();
            $table->boolean('lift_chains')->nullable();
            $table->boolean('fork')->nullable();
            $table->boolean('body_unit')->nullable();
            $table->boolean('lampu_kombinasi_kiri')->nullable();
            $table->boolean('lampu_kombinasi_kanan')->nullable();
            $table->boolean('lampu_sorot')->nullable();
            $table->boolean('lampu_sign_depan_kanan')->nullable();
            $table->boolean('lampu_sign_depan_kiri')->nullable();
            $table->boolean('klakson')->nullable();
            $table->boolean('buzzer_back')->nullable();
            $table->boolean('kaca_spion')->nullable();
            $table->boolean('baut_roda')->nullable();
            $table->boolean('ban')->nullable();
            $table->boolean('kebersihan_unit')->nullable();
            $table->boolean('panel_display')->nullable();
            $table->boolean('sistem_kemudi')->nullable();
            $table->string('hours_meter')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_electric_p2h_inspections');
    }
};
