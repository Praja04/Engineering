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
        Schema::create('cal_timbangan_kemampuan_ulang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->onDelete('cascade');
            $table->enum('jenis', ['mendekati_nol', 'setengah_kapasitas', 'full_kapasitas'])->nullable();
            $table->integer('ulangan')->nullable();
            $table->decimal('massa', 15, 4)->nullable();
            $table->decimal('nilai_z', 15, 4)->nullable();
            $table->decimal('nilai_m', 15, 4)->nullable();
            $table->decimal('selisih', 15, 4)->nullable();
            $table->decimal('maks_perbedaan', 15, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('cal_timbangan_keseragaman_skala', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->onDelete('cascade');
            $table->integer('massa_ke'); // 1 sampai 9
            $table->enum('jenis', ['Z', 'M1', 'M2']);

            $table->decimal('beban', 15, 8)->nullable();
            $table->decimal('pembacaan', 15, 8)->nullable();
            $table->timestamps();
        });

        Schema::create('cal_timbangan_pinggan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->onDelete('cascade');
            $table->decimal('diameter', 10, 4)->nullable();
            $table->decimal('massa', 10, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('cal_timbangan_pinggan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinggan_id')->constrained('cal_timbangan_pinggan')->onDelete('cascade');
            $table->integer('percobaan');
            $table->enum('posisi', ['tengah', 'depan', 'belakang', 'kiri', 'kanan'])->nullable();
            $table->decimal('nilai', 10, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('cal_timbangan_tare', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->onDelete('cascade');
            $table->enum('kondisi', ['tanpa', 'dengan'])->nullable();
            $table->enum('label', ['zero_1', 'm_1', 'm_2', 'zero_2'])->nullable();
            $table->decimal('nilai', 10, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('cal_timbangan_histerisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->onDelete('cascade');
            $table->enum('label', ['z1', 'm1', 'm_plus', 'm2', 'z2'])->nullable();
            $table->integer('pengulangan')->nullable();
            $table->decimal('nilai', 10, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('cal_timbangan_master', function (Blueprint $table) {
            $table->id();
            $table->decimal('beban', 10, 3);
            $table->decimal('standar_massa', 10, 6);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_timbangan_kemampuan_ulang');
        Schema::dropIfExists('cal_timbangan_keseragaman_skala');
        Schema::dropIfExists('cal_timbangan_pinggan_detail');
        Schema::dropIfExists('cal_timbangan_pinggan');
        Schema::dropIfExists('cal_timbangan_tare');
        Schema::dropIfExists('cal_timbangan_histerisis');
        Schema::dropIfExists('cal_timbangan_master');
    }
};
