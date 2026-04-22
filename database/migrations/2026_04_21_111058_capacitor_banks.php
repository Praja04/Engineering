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
        //
        Schema::create('capacitor_banks', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal');
            $table->time('jam')->nullable();

            $table->decimal('arus_total', 10, 2)->nullable();

            // ── CAPASITOR A ─────────────────────────
            $table->integer('cap_a_nomor')->nullable();
            $table->decimal('cap_a_i1', 10, 2)->nullable();
            $table->decimal('cap_a_i2', 10, 2)->nullable();
            $table->decimal('cap_a_i3', 10, 2)->nullable();

            // ── CAPASITOR B ─────────────────────────
            $table->integer('cap_b_nomor')->nullable();
            $table->decimal('cap_b_i1', 10, 2)->nullable();
            $table->decimal('cap_b_i2', 10, 2)->nullable();
            $table->decimal('cap_b_i3', 10, 2)->nullable();

            // ── CAPASITOR C ─────────────────────────
            $table->integer('cap_c_nomor')->nullable();
            $table->decimal('cap_c_i1', 10, 2)->nullable();
            $table->decimal('cap_c_i2', 10, 2)->nullable();
            $table->decimal('cap_c_i3', 10, 2)->nullable();

            $table->decimal('suhu_ruang', 5, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('capasitor_banks');
    }
};
