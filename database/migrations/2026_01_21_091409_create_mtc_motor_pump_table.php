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
        Schema::create('mtc_motor_pump', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mesin');
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('paket')->nullable();

            // Motor
            $table->boolean('electrical_motor')->nullable();
            $table->boolean('putaran_motor')->nullable();
            $table->boolean('fibrasi_suara_motor')->nullable();
            $table->boolean('bearing_motor')->nullable();
            $table->boolean('pelumasan_motor')->nullable();
            $table->boolean('kebersihan_unit_body_motor')->nullable();

            // Pompa
            $table->boolean('putaran_pompa')->nullable();
            $table->boolean('shaft_karet_coupling_pompa')->nullable();
            $table->boolean('fan_belt_pompa')->nullable();
            $table->boolean('pressure_pompa')->nullable();
            $table->boolean('mechanical_seal_pompa')->nullable();
            $table->boolean('gasket_pompa')->nullable();
            $table->boolean('impeler')->nullable();
            $table->boolean('kebersihan_unit_body_pompa')->nullable();

            // Aksesoris
            $table->boolean('valve_aksesoris')->nullable();
            $table->boolean('cek_valve_aksesoris')->nullable();
            $table->boolean('flow_meter_aksesoris')->nullable();
            $table->boolean('strainer_aksesoris')->nullable();
            $table->boolean('alat_ukur_aksesoris')->nullable();
            $table->boolean('kelengkapan_baut_mur_aksesoris')->nullable();

            // Gearbox
            $table->boolean('tambah_ganti_oli_gearbox')->nullable();
            $table->boolean('unit_area_gearbox')->nullable();
            $table->boolean('oil_seal_gearbox')->nullable();
            $table->boolean('filter_udara_gearbox')->nullable();
            $table->boolean('bearing_gearbox')->nullable();

            $table->text('keterangan')->nullable();
            $table->string('korektif')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_master_mesin');
    }
};
