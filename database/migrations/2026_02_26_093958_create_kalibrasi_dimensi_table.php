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
        Schema::create('cal_dimensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')
                ->constrained('cal_main')
                ->cascadeOnDelete();

            $table->decimal('titik_kalibrasi', 10, 3)->nullable();
            $table->decimal('nilai_master', 10, 3)->nullable(); // ambil dari avg nilai penunjuk standar
            $table->decimal('avg_pembacaan', 10, 3)->nullable();
            $table->decimal('koreksi', 10, 3)->nullable();
            $table->decimal('std_dev', 10, 3)->nullable();
            $table->decimal('ketidakpastian', 10, 3)->nullable();
            $table->timestamps();
        });

        // Tabel kalibrasi_dimensi_gabungan
        Schema::create('cal_dimensi_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dimensi_id')
                ->constrained('cal_dimensi')
                ->cascadeOnDelete();

            $table->decimal('penunjuk_standar', 10, 3);
            $table->decimal('penunjuk_alat', 10, 3);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_dimensi_detail');
        Schema::dropIfExists('cal_dimensi');
    }
};
