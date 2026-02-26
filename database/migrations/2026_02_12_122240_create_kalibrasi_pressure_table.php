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
        Schema::create('cal_pressure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')
                ->constrained('cal_main')
                ->cascadeOnDelete();

            $table->decimal('titik_kalibrasi', 10, 3);

            // Average
            $table->decimal('avg_penunjuk_alat_naik', 10, 3)->nullable();
            $table->decimal('avg_penunjuk_alat_turun', 10, 3)->nullable();
            $table->decimal('avg_tekanan_standar_naik', 10, 3)->nullable();
            $table->decimal('avg_tekanan_standar_turun', 10, 3)->nullable();
            $table->decimal('avg_koreksi_alat_naik', 10, 3)->nullable();
            $table->decimal('avg_koreksi_alat_turun', 10, 3)->nullable();

            // Standard Deviation
            $table->decimal('std_deviasi_naik', 10, 6)->nullable();
            $table->decimal('std_deviasi_turun', 10, 6)->nullable();

            $table->decimal('ketidakpastian_naik', 10, 6)->nullable();
            $table->decimal('ketidakpastian_turun', 10, 6)->nullable();

            // Uncertainty
            $table->decimal('u_naik', 12, 9)->nullable();
            $table->decimal('u_turun', 12, 9)->nullable();
            $table->decimal('u_naik_kuadrat', 12, 9)->nullable();
            $table->decimal('u_turun_kuadrat', 12, 9)->nullable();
            $table->decimal('u_gabungan', 12, 9)->nullable();

            $table->timestamps();
        });

        Schema::create('cal_pressure_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pressure_id')
                ->constrained('cal_pressure')
                ->cascadeOnDelete();

            $table->enum('arah', ['naik', 'turun']);

            $table->decimal('penunjuk_standar', 10, 3);
            $table->decimal('penunjuk_alat', 10, 3);
            $table->decimal('koreksi_standar', 10, 3)->default(0);
            $table->decimal('tekanan_standar', 10, 3)->nullable();
            $table->decimal('koreksi_alat', 10, 3)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_pressure_detail');
        Schema::dropIfExists('cal_pressure');
    }
};
