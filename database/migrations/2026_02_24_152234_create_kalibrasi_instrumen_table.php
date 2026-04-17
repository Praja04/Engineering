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
        Schema::create('cal_instrumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->cascadeOnDelete();
            $table->string('titik_kalibrasi')->nullable();
            $table->string('indikator')->nullable();
            $table->string('jenis_alat_ukur')->nullable();
            $table->string('jenis_standar')->nullable();
            $table->decimal('nilai_master', 10, 4)->nullable();
            $table->decimal('avg_pembacaan', 10, 4)->nullable();
            $table->decimal('std_dev', 10, 4)->nullable();
            $table->decimal('koreksi', 10, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('cal_instrumen_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('instrumen_id')
                ->constrained('cal_instrumen')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('no_ulang'); // 1–5

            $table->decimal('alat', 12, 6)->nullable();
            $table->decimal('standar', 12, 6)->nullable();
            $table->decimal('pembacaan_alat', 12, 6)->nullable();
            $table->decimal('pembacaan_standar', 12, 6)->nullable();

            $table->timestamps();
        });

        Schema::create('cal_instrumen_keypad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->cascadeOnDelete();
            $table->boolean('tested')->nullable();
            $table->string('measured')->nullable();
            $table->string('criterion')->nullable();
            $table->string('passed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_instrumen_keypad');
        Schema::dropIfExists('cal_instrumen_detail');
        Schema::dropIfExists('cal_instrumen');
    }
};
