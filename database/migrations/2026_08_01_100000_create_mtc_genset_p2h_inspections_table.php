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
        Schema::create('mtc_genset_p2h_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mtc_master_mesin')->onDelete('restrict');
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->onDelete('cascade');
            $table->integer('shift');
            $table->string('no_unit')->nullable();
            $table->text('catatan')->nullable();

            $table->boolean('level_oli_mesin')->nullable();
            $table->boolean('kebocoran_oli_mesin')->nullable();
            $table->boolean('level_coolant_radiator')->nullable();
            $table->boolean('kebocoran_coolant')->nullable();
            $table->boolean('level_bahan_bakar')->nullable();
            $table->boolean('kebocoran_bahan_bakar')->nullable();
            $table->boolean('kondisi_aki_baterai')->nullable();
            $table->boolean('tegangan_baterai')->nullable();
            $table->boolean('filter_udara')->nullable();
            $table->boolean('kondisi_panel_genset')->nullable();
            $table->boolean('emergency_stop')->nullable();
            $table->boolean('suara_mesin_running')->nullable();
            $table->boolean('kebersihan_area_genset')->nullable();
            $table->boolean('kondisi_knalpot_exhaust')->nullable();

            $table->string('hours_meter')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_genset_p2h_inspections');
    }
};
