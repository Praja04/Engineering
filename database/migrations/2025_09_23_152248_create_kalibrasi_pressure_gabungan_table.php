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
        Schema::create('kalibrasi_pressure_gabungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->cascadeOnDelete();
            $table->float('titik_kalibrasi', 8, 3);

            // avg per arah
            $table->float('avg_penunjuk_alat_naik', 8, 3)->nullable();
            $table->float('avg_penunjuk_alat_turun', 8, 3)->nullable();
            $table->float('avg_tekanan_standar_naik', 8, 3)->nullable();
            $table->float('avg_tekanan_standar_turun', 8, 3)->nullable();
            $table->float('avg_kor_alat_naik', 8, 3)->nullable();
            $table->float('avg_kor_alat_turun', 8, 3)->nullable();

            // std deviasi per arah
            $table->float('std_deviasi_naik', 8, 3)->nullable();
            $table->float('std_deviasi_turun', 8, 3)->nullable();

            // ketidakpastian per arah
            $table->float('ketidak_pastian_naik', 8, 3)->nullable();
            $table->float('ketidak_pastian_turun', 8, 3)->nullable();

            // U per arah
            $table->decimal('u_naik', 10, 9)->nullable();
            $table->decimal('u_turun', 10, 9)->nullable();
            $table->decimal('u_naik_kuadrat', 10, 9)->nullable();
            $table->decimal('u_turun_kuadrat', 10, 9)->nullable();
            $table->decimal('u_gabungan', 10, 9)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasi_pressure_gabungan');
    }
};
