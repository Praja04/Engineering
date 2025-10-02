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
        Schema::create('kalibrasi_pressure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->cascadeOnDelete();
            $table->float('titik_kalibrasi', 8, 3);
            $table->enum('tekanan', ['naik', 'turun']);
            $table->float('penunjuk_standar', 8, 3)->nullable();
            $table->float('penunjuk_alat', 8, 3)->nullable();
            $table->float('koreksi_standar', 8, 3)->nullable();
            $table->float('tekanan_standar', 8, 3)->nullable();
            $table->float('koreksi_alat', 8, 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasi_pressure');
    }
};
