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
        Schema::create('kalibrasi_tmb_pembacaan_smry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->string('kemampuan')->nullable();
            $table->string('beban')->nullable();
            $table->decimal('std_dev', 10, 3)->nullable();
            $table->decimal('maks_perbedaan_akhir', 10, 3)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_tmb_keseragaman_skala_smry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('beban', 10, 3)->nullable();
            $table->decimal('avg_z', 10, 4)->nullable();
            $table->decimal('avg_m', 10, 4)->nullable();
            $table->decimal('selisih_zm', 10, 4)->nullable();
            $table->decimal('standar_massa', 10, 4)->nullable();
            $table->decimal('koreksi_skala', 10, 4)->nullable();
            $table->decimal('absolut_koreksi', 10, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_tmb_pinggan_smry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->integer('percobaan')->nullable();
            $table->decimal('smry_tengah', 10, 4)->nullable();
            $table->decimal('smry_depan', 10, 4)->nullable();
            $table->decimal('smry_belakang', 10, 4)->nullable();
            $table->decimal('smry_kiri', 10, 4)->nullable();
            $table->decimal('smry_kanan', 10, 4)->nullable();
            $table->decimal('minimum', 10, 4)->nullable();
            $table->decimal('maximum', 10, 4)->nullable();
            $table->decimal('selisih_maks', 10, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_tmb_tare_smry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('massa', 10, 3)->nullable();
            $table->decimal('selisih_mz_tanpa_nol', 10, 3)->nullable();
            $table->decimal('selisih_mz_dengan_nol', 10, 3)->nullable();
            $table->decimal('pengaruh', 10, 3)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_tmb_histerisis_smry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('pembacaan_terkecil', 10, 3)->nullable();
            $table->decimal('setengah_kapasitas', 10, 3)->nullable();
            $table->decimal('avg_m1m2', 10, 3)->nullable();
            $table->decimal('avg_z1z2', 10, 3)->nullable();
            $table->decimal('avg_mz', 10, 3)->nullable();
            $table->decimal('histerisis', 10, 3)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_tmb_ketidakpastian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('pembacaan_terkecil', 10, 3)->nullable();
            $table->decimal('setengah_kapasitas', 10, 3)->nullable();
            $table->integer('percobaan')->nullable();
            $table->decimal('m1m2', 10, 3)->nullable();
            $table->decimal('z1z2', 10, 3)->nullable();
            $table->decimal('avg_m1m2', 10, 3)->nullable();
            $table->decimal('avg_z1z2', 10, 3)->nullable();
            $table->decimal('histeris', 10, 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasi_tmb_pembacaan_smry');
        Schema::dropIfExists('kalibrasi_tmb_keseragaman_skala_smry');
        Schema::dropIfExists('kalibrasi_tmb_pinggan_smry');
        Schema::dropIfExists('kalibrasi_tmb_tare_smry');
        Schema::dropIfExists('kalibrasi_tmb_histerisis_smry');
        Schema::dropIfExists('kalibrasi_tmb_ketidakpastian');
    }
};
