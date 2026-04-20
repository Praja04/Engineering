<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('esp_operational_reports', function (Blueprint $table) {
            $table->id();

            // Tanggal laporan (berdasarkan shift kerja, mulai jam 06:00)
            $table->date('tanggal_laporan');

            // Jam input data (format: 06:00 s/d 05:00 hari berikutnya)
            $table->time('jam_laporan');

            // Grup kerja (A, B, C, D)
            $table->enum('grup', ['A', 'B', 'C', 'D']);

            // Parameter ESP
            $table->decimal('arus_primer', 8, 2)->nullable();      // Arus Primer (Ampere)
            $table->decimal('arus_sekunder', 8, 2)->nullable();    // Arus Sekunder (Ampere)
            $table->decimal('tegangan_primer', 10, 2)->nullable(); // Tegangan Primer (Volt)
            $table->decimal('tegangan_sekunder', 10, 2)->nullable(); // Tegangan Sekunder (Volt)
            $table->decimal('suhu_thermal', 5, 2)->nullable();     // Suhu (°C)

            $table->timestamps();

            // Mencegah data dobel dalam 1 jam & grup yang sama
            $table->unique(['tanggal_laporan', 'jam_laporan', 'grup']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esp_operational_reports');
    }
};
