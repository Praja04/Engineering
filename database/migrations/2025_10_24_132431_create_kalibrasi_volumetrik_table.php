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
        // Tabel kalibrasi_volumtrik
        Schema::create('kalibrasi_volumetrik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('titik_kalibrasi', 10, 4)->nullable();
            $table->decimal('penunjuk_standar', 10, 4)->nullable();
            $table->decimal('penunjuk_alat', 10, 4)->nullable();
            $table->decimal('koreksi', 10, 4)->nullable();
            $table->timestamps();
        });

        // Tabel kalibrasi_volumetrik_gabungan
        Schema::create('kalibrasi_volumetrik_gabungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('avg_penunjuk_standar', 10, 9)->nullable();
            $table->decimal('avg_koreksi', 10, 9)->nullable();
            $table->decimal('stdev_penunjuk_standar', 10, 9)->nullable();
            $table->decimal('akar_10', 10, 9)->nullable();
            $table->decimal('u_timbangan', 10, 9)->nullable();
            $table->decimal('u_total', 10, 9)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasi_volumetrik_gabungan');
        Schema::dropIfExists('kalibrasi_volumetrik');
    }
};
