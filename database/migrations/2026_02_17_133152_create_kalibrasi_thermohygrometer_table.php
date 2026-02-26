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
        Schema::create('cal_thermohygrometer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->cascadeOnDelete();
            $table->decimal('titik_kalibrasi', 10, 3)->nullable();
            $table->string('posisi')->nullable();

            $table->decimal('avg_penunjuk_alat_suhu', 10, 3)->nullable();
            $table->decimal('avg_tekanan_standar_suhu', 10, 3)->nullable();
            $table->decimal('avg_kor_alat_suhu', 10, 3)->nullable();
            $table->decimal('std_deviasi_suhu', 10, 3)->nullable();
            $table->decimal('ketidak_pastian_suhu', 10, 3)->nullable();

            // rh
            $table->decimal('avg_penunjuk_alat_rh', 10, 3)->nullable();
            $table->decimal('avg_tekanan_standar_rh', 10, 3)->nullable();
            $table->decimal('avg_kor_alat_rh', 10, 3)->nullable();
            $table->decimal('std_deviasi_rh', 10, 3)->nullable();
            $table->decimal('ketidak_pastian_rh', 10, 3)->nullable();


            $table->timestamps();
        });

        Schema::create('cal_thermohygrometer_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thermohygro_id')->constrained('cal_thermohygrometer')->cascadeOnDelete();

            $table->unsignedTinyInteger('urutan'); // 0,1,2 (3x pengukuran)

            // SUHU
            $table->decimal('penunjuk_standar_suhu', 10, 3)->nullable();
            $table->decimal('penunjuk_alat_suhu', 10, 3)->nullable();

            // RH
            $table->decimal('penunjuk_standar_rh', 10, 3)->nullable();
            $table->decimal('penunjuk_alat_rh', 10, 3)->nullable();

            $table->decimal('koreksi_standar_suhu', 10, 3)->nullable();
            $table->decimal('koreksi_standar_rh', 10, 3)->nullable();

            $table->decimal('tekanan_standar_suhu', 10, 3)->nullable();
            $table->decimal('tekanan_standar_rh', 10, 3)->nullable();

            $table->decimal('koreksi_alat_suhu', 10, 3)->nullable();
            $table->decimal('koreksi_alat_rh', 10, 3)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_thermohygrometer_detail');
        Schema::dropIfExists('cal_thermohygrometer');
    }
};
