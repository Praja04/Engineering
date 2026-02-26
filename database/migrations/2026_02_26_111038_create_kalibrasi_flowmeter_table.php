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
        Schema::create('cal_flowmeter', function (Blueprint $table) {
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

        // Tabel kalibrasi_flow_meter_gabungan
        Schema::create('cal_flowmeter_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('flowmeter_id')
                ->constrained('cal_flowmeter')
                ->cascadeOnDelete();

            $table->decimal('penunjuk_standar', 10, 3);
            $table->decimal('penunjuk_alat', 10, 3);
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_flowmeter_detail');
        Schema::dropIfExists('cal_flowmeter');
    }
};
