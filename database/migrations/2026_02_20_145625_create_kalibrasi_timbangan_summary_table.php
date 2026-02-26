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
        Schema::create('cal_timbangan_kemampuan_ulang_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->onDelete('cascade');
            $table->enum('jenis', ['mendekati_nol', 'setengah_kapasitas', 'full_kapasitas'])->nullable();
            $table->decimal('massa', 15, 3)->nullable();
            $table->decimal('std_dev', 10, 3)->nullable();
            $table->decimal('maks_perbedaan_akhir', 10, 3)->nullable();
            $table->timestamps();
        });

        Schema::create('cal_timbangan_keseragaman_skala_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->cascadeOnDelete();

            $table->integer('massa_ke');
            $table->decimal('beban', 15, 8)->nullable();

            $table->decimal('avg_z', 15, 8)->nullable();
            $table->decimal('avg_m', 15, 8)->nullable();

            $table->decimal('selisih_zm', 15, 8)->nullable();
            $table->decimal('koreksi_skala', 15, 8)->nullable();
            $table->decimal('absolut_koreksi', 15, 8)->nullable();

            $table->timestamps();
        });

        Schema::create('cal_timbangan_pinggan_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->onDelete('cascade');
            $table->integer('percobaan')->nullable();
            $table->decimal('summary_tengah', 10, 4)->nullable();
            $table->decimal('summary_depan', 10, 4)->nullable();
            $table->decimal('summary_belakang', 10, 4)->nullable();
            $table->decimal('summary_kiri', 10, 4)->nullable();
            $table->decimal('summary_kanan', 10, 4)->nullable();
            $table->decimal('minimum', 10, 4)->nullable();
            $table->decimal('maximum', 10, 4)->nullable();
            $table->decimal('selisih_maks', 10, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('cal_timbangan_tare_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->onDelete('cascade');
            $table->enum('kondisi', ['tanpa', 'dengan'])->nullable();
            $table->decimal('massa', 10, 4)->nullable();
            $table->decimal('selisih_mz', 10, 4)->nullable();
            $table->decimal('pengaruh', 10, 3)->nullable();
            $table->timestamps();
        });

        Schema::create('cal_timbangan_histerisis_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->onDelete('cascade');
            $table->decimal('pembacaan_terkecil', 10, 4)->nullable();
            $table->decimal('setengah_kapasitas', 10, 4)->nullable();
            $table->decimal('avg_m1m2', 10, 4)->nullable();
            $table->decimal('avg_z1z2', 10, 4)->nullable();
            $table->decimal('nilai_mz', 10, 4)->nullable(); // avg_m1m2 - avg_z1z2
            $table->decimal('histerisis', 10, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('cal_timbangan_ketidakpastian_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->onDelete('cascade');
            $table->decimal('kapasitas_alat', 15, 4)->nullable();
            $table->decimal('pembacaan_terkecil', 15, 4)->nullable();
            $table->decimal('timbangan_standar', 15, 4)->nullable();
            $table->decimal('skala_terkecil', 15, 4)->nullable();
            $table->decimal('max_kemampuan_ulang', 15, 4)->nullable();
            $table->decimal('drift', 15, 4)->nullable();
            $table->decimal('bouyancy', 15, 4)->nullable();
            $table->decimal('ketidakpastian_gabungan', 15, 4)->nullable();
            $table->decimal('ketidakpastian_perluas', 15, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_timbangan_kemampuan_ulang_summaries');
        Schema::dropIfExists('cal_timbangan_keseragaman_skala_summaries');
        Schema::dropIfExists('cal_timbangan_pinggan_summaries');
        Schema::dropIfExists('cal_timbangan_tare_summaries');
        Schema::dropIfExists('cal_timbangan_histerisis_summaries');
        Schema::dropIfExists('cal_timbangan_ketidakpastian_summaries');
    }
};
