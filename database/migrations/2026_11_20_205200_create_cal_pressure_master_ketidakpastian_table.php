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
        Schema::create('cal_pressure_master_ketidakpastian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_id')
                ->nullable()
                ->constrained('alat_kalibrasi')
                ->nullOnDelete();

            $table->string('kode_alat', 100)->index();
            $table->string('nama_alat', 255)->nullable();
            $table->decimal('titik_kalibrasi', 12, 4);
            $table->decimal('nilai_ketidakpastian', 12, 6);
            $table->decimal('u_naik', 12, 6)->nullable();
            $table->decimal('u_turun', 12, 6)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            // Kombinasi kode_alat dan titik_kalibrasi bersifat unik
            $table->unique(['kode_alat', 'titik_kalibrasi'], 'uq_kode_titik_pressure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_pressure_master_ketidakpastian');
    }
};
