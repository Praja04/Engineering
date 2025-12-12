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
        Schema::create('kalibrasi_jangka_sorong_master', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->decimal('nilai_master', 10, 4);
            $table->timestamps();
        });

        Schema::create('kalibrasi_jangka_sorong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->foreignId('master_id')->constrained('kalibrasi_jangka_sorong_master')->onDelete('cascade');
            $table->integer('no')->nullable();
            $table->decimal('nilai_master', 10, 4);
            $table->decimal('nilai_pembacaan', 10, 4);
            $table->timestamps();
        });

        Schema::create('kalibrasi_jangka_sorong_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->foreignId('master_id')->constrained('kalibrasi_jangka_sorong_master')->onDelete('cascade');
            $table->decimal('avg_pembacaan', 10, 4);
            $table->decimal('std_dev', 10, 4);
            $table->decimal('koreksi', 10, 4);
            $table->timestamps();
        });

        Schema::create('kalibrasi_jangka_sorong_final_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('kalibrasi')->onDelete('cascade');
            $table->decimal('std_dev_total', 10, 5);
            $table->decimal('ketidakpastian', 10, 4);
            $table->decimal('k_2', 10, 4)->default(2.0000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasi_jangka_sorong_master');
        Schema::dropIfExists('kalibrasi_jangka_sorong');
        Schema::dropIfExists('kalibrasi_jangka_sorong_summary');
        Schema::dropIfExists('kalibrasi_jangka_sorong_final_summary');
    }
};
