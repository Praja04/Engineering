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
        Schema::create('kalibrasi_timbangan_pembacaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->string('kemampuan');
            $table->decimal('titik', 10, 2)->nullable();
            $table->integer('ulangan')->nullable();
            $table->decimal('pembacaan_z', 10, 2)->nullable();
            $table->decimal('pembacaan_m', 10, 2)->nullable();
            $table->decimal('selisih', 10, 2)->nullable();
            $table->decimal('maks_perbedaan', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_timbangan_keseragaman_skala', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('massa', 10, 2)->nullable();
            $table->decimal('beban', 10, 2)->nullable();
            $table->string('beban_timbangan')->nullable();
            $table->decimal('pembacaan_skala', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_timbangan_pinggan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('diameter', 10, 2)->nullable();
            $table->decimal('massa', 10, 2)->nullable();
            $table->integer('percobaan')->nullable();
            $table->decimal('tengah', 10, 2)->nullable();
            $table->decimal('depan', 10, 2)->nullable();
            $table->decimal('belakang', 10, 2)->nullable();
            $table->decimal('kiri', 10, 2)->nullable();
            $table->decimal('kanan', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_timbangan_tare', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('massa', 10, 3)->nullable();
            $table->string('tipe_tare')->nullable();
            $table->string('beban')->nullable();
            $table->decimal('pembacaan', 10, 3)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_timbangan_histerisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('pembacaan_terkecil', 10, 3)->nullable();
            $table->decimal('setengah_kapasitas', 10, 3)->nullable();
            $table->integer('percobaan')->nullable();
            $table->decimal('z1', 10, 3)->nullable();
            $table->decimal('m1', 10, 3)->nullable();
            $table->decimal('m_m', 10, 3)->nullable();
            $table->decimal('m2', 10, 3)->nullable();
            $table->decimal('z2', 10, 3)->nullable();
            $table->decimal('m1_m2', 10, 3)->nullable();
            $table->decimal('z1_z2', 10, 3)->nullable();
            $table->timestamps();
        });

        Schema::create('kalibrasi_timbangan_master', function (Blueprint $table) {
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
        Schema::dropIfExists('kalibrasi_timbangan_pembacaan');
        Schema::dropIfExists('kalibrasi_timbangan_keseragaman_skala');
        Schema::dropIfExists('kalibrasi_timbangan_pinggan');
        Schema::dropIfExists('kalibrasi_timbangan_tare');
        Schema::dropIfExists('kalibrasi_timbangan_histerisis');
        Schema::dropIfExists('kalibrasi_timbangan_master');
    }
};
