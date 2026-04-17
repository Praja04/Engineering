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
        Schema::create('cal_volumetrik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')
                ->constrained('cal_main')
                ->cascadeOnDelete();

            $table->decimal('titik_kalibrasi', 10, 3)->nullable();
            $table->decimal('avg_penunjuk_standar', 10, 9)->nullable();
            $table->decimal('avg_koreksi', 10, 9)->nullable();
            $table->decimal('stdev_penunjuk_standar', 10, 9)->nullable();
            $table->decimal('akar_10', 10, 9)->nullable();
            $table->decimal('u_timbangan', 10, 9)->nullable();
            $table->decimal('u_total', 10, 9)->nullable();
            $table->timestamps();
        });

        // Tabel kalibrasi_volumetrik_gabungan
        Schema::create('cal_volumetrik_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('volumetrik_id')
                ->constrained('cal_volumetrik')
                ->cascadeOnDelete();

            $table->decimal('penunjuk_standar', 10, 3);
            $table->decimal('penunjuk_alat', 10, 3);
            $table->decimal('koreksi', 10, 3)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_volumetrik_detail');
        Schema::dropIfExists('cal_volumetrik');
    }
};
