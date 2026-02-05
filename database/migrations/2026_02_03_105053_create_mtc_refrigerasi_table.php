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
        Schema::create('mtc_refrigerasi_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mtc_master_mesin')->onDelete('restrict');
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->onDelete('cascade');

            // Unit Indoor
            $table->boolean('check_filter_udara')->nullable();
            $table->boolean('check_cover_filter_udara')->nullable();
            $table->boolean('check_electrical_indoor')->nullable();
            $table->boolean('check_suhu_evaporator')->nullable();
            $table->boolean('check_indikator_display')->nullable();
            $table->boolean('check_motor_blower')->nullable();
            $table->boolean('check_fan_belt_blower')->nullable();
            $table->boolean('check_pergerakan_motor_swing')->nullable();
            $table->boolean('check_kontroler_indoor')->nullable();
            $table->boolean('check_saluran_drain_kondensasi')->nullable();
            $table->boolean('sirkulasi_evaporator')->nullable();

            // Unit Outdoor
            $table->boolean('check_kondisi_kondensor')->nullable();
            $table->boolean('check_electrical_outdoor')->nullable();
            $table->boolean('check_motor_fan')->nullable();
            $table->boolean('check_tekanan_freon')->nullable();
            $table->boolean('pelumasan_motor_fan')->nullable();
            $table->boolean('kebersihan_unit_body_outdoor')->nullable();

            // Jalur Distribusi
            $table->boolean('check_jalur_freon')->nullable();
            $table->boolean('check_jalur_distribusi_udara')->nullable();
            $table->boolean('check_jalur_return_udara')->nullable();

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
        Schema::dropIfExists('mtc_refrigerasi_inspections');
    }
};
