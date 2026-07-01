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
        Schema::create('capacitor_bank_machine_data', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal')->nullable();
            $table->string('cap_type')->nullable();
            $table->decimal('current', 12, 3)->nullable();

            // ── VOLTAGE LINE-TO-LINE ─────────────────────────────
            $table->decimal('voltage_ll_Vab', 12, 3)->nullable();
            $table->decimal('voltage_ll_Vbc', 12, 3)->nullable();
            $table->decimal('voltage_ll_Vca', 12, 3)->nullable();

            // ── VOLTAGE LINE-TO-NEUTRAL ──────────────────────────
            $table->decimal('voltage_ln_Van', 12, 3)->nullable();
            $table->decimal('voltage_ln_Vbn', 12, 3)->nullable();
            $table->decimal('voltage_ln_Vcn', 12, 3)->nullable();

            // ── POWER ────────────────────────────────────────────
            $table->decimal('power_Ptot', 12, 3)->nullable();
            $table->decimal('power_Qtot', 12, 3)->nullable();
            $table->decimal('power_Stot', 12, 3)->nullable();

            // ── POWER FACTOR ─────────────────────────────────────
            $table->decimal('pf_PFa', 10, 4)->nullable();
            $table->decimal('pf_PFb', 10, 4)->nullable();
            $table->decimal('pf_PFc', 10, 4)->nullable();

            // ── COS PHI ──────────────────────────────────────────
            $table->decimal('cosphi_dPFa', 10, 4)->nullable();
            $table->decimal('cosphi_dPFb', 10, 4)->nullable();
            $table->decimal('cosphi_dPFc', 10, 4)->nullable();

            // ── FREQUENCY ────────────────────────────────────────
            $table->decimal('freq', 8, 3)->nullable();

            // ── THD CURRENT ──────────────────────────────────────
            $table->decimal('thd_i_Ia', 20, 3)->nullable();
            $table->decimal('thd_i_Ib', 20, 3)->nullable();
            $table->decimal('thd_i_Ic', 20, 3)->nullable();

            // ── THD VOLTAGE ──────────────────────────────────────
            $table->decimal('thd_v_Van', 20, 3)->nullable();
            $table->decimal('thd_v_Vbn', 20, 3)->nullable();
            $table->decimal('thd_v_Vcn', 30, 3)->nullable();

            $table->timestamps();
        });

        Schema::create('capacitor_bank_cap_histories', function (Blueprint $table) {
            $table->id();

            // Tanggal pencatatan (date only — untuk grouping per hari)
            $table->date('tanggal')->nullable();

            // cap1–cap12 status: 0 = OFF, 1 = ON
            $table->tinyInteger('cap1')->nullable();
            $table->tinyInteger('cap2')->nullable();
            $table->tinyInteger('cap3')->nullable();
            $table->tinyInteger('cap4')->nullable();
            $table->tinyInteger('cap5')->nullable();
            $table->tinyInteger('cap6')->nullable();
            $table->tinyInteger('cap7')->nullable();
            $table->tinyInteger('cap8')->nullable();
            $table->tinyInteger('cap9')->nullable();
            $table->tinyInteger('cap10')->nullable();
            $table->tinyInteger('cap11')->nullable();
            $table->tinyInteger('cap12')->nullable();

            // Snapshot waktu data diterima dari mesin
            $table->timestamp('recorded_at')->nullable();

            $table->timestamps();

            // Index untuk query per hari
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capacitor_bank_machine_data');
        Schema::dropIfExists('capacitor_bank_cap_histories');
    }
};
