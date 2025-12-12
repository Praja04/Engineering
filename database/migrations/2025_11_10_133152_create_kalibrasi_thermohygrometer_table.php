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
        Schema::create('kalibrasi_thermohygrometer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->cascadeOnDelete();
            $table->decimal('titik_kalibrasi', 10, 3);
            $table->string('posisi');
            $table->enum('tipe_hitung', ['suhu', 'rh']);
            $table->decimal('penunjuk_standar', 10, 3)->nullable();
            $table->decimal('penunjuk_alat', 10, 3)->nullable();
            $table->decimal('koreksi_standar', 10, 3)->nullable();
            $table->decimal('tekanan_standar', 10, 3)->nullable();
            $table->decimal('koreksi_alat', 10, 3)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_thermohygrometer_gab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->cascadeOnDelete();
            $table->decimal('titik_kalibrasi', 10, 3);
            $table->string('posisi');

            // suhu
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasi_thermohygrometer');
        Schema::dropIfExists('kalibrasi_thermohygrometer_gab');
    }
};
