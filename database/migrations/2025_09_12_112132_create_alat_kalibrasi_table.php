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
        Schema::create('alat_kalibrasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('jenis_kalibrasi');
            $table->integer('jumlah');
            $table->string('kode_alat');
            $table->string('nama_alat');
            $table->string('departemen_pemilik');
            $table->string('lokasi_alat');
            $table->string('no_kalibrasi');
            $table->string('merk');
            $table->string('tipe');
            $table->float('kapasitas');
            $table->float('resolusi');
            $table->string('range_use');
            $table->float('limits_permissible_error');
            $table->timestamps();

            $table->unique('kode_alat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat_kalibrasi');
    }
};
