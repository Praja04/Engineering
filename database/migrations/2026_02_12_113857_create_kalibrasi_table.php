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
        Schema::create('cal_main', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_id')->constrained('alat_kalibrasi')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('jenis_kalibrasi');
            $table->string('lokasi_kalibrasi');
            $table->string('suhu_ruangan');
            $table->string('kelembaban');
            $table->date('tgl_kalibrasi');
            $table->date('tgl_kalibrasi_ulang');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_main');
    }
};
