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
        Schema::create('kalibrasi_temperature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->cascadeOnDelete();
            $table->decimal('titik_kalibrasi', 10, 3);
            $table->decimal('penunjuk_standar', 10, 3)->nullable();
            $table->decimal('penunjuk_alat', 10, 3)->nullable();
            $table->decimal('koreksi_standar', 10, 3)->nullable();
            $table->decimal('suhu_standar', 10, 3)->nullable();
            $table->decimal('koreksi_alat', 10, 3)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_temperature_gab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('titik_kalibrasi', 10, 3)->nullable();
            $table->decimal('avg_penunjuk_alat', 10, 3)->nullable();
            $table->decimal('avg_suhu_standar', 10, 3)->nullable();
            $table->decimal('avg_kor_alat', 10, 3)->nullable();
            $table->decimal('stdev', 10, 3)->nullable();
            $table->decimal('ketidakpastian', 10, 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasi_temperature');
        Schema::dropIfExists('kalibrasi_temperature_gab');
    }
};
