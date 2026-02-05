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
        Schema::create('mtc_electrical_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mtc_master_mesin')->onDelete('restrict');
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->onDelete('cascade');

            // Panel
            $table->boolean('check_kunci')->nullable();
            $table->boolean('check_koneksi_kabel')->nullable();
            $table->boolean('check_wiring_panel')->nullable();
            $table->boolean('check_lampu_indikator')->nullable();
            $table->boolean('check_name_plate')->nullable();
            $table->boolean('check_unit_electrical')->nullable();
            $table->boolean('check_grounding')->nullable();
            $table->boolean('check_kebersihan')->nullable();
            $table->boolean('check_bus_bar')->nullable();
            $table->boolean('check_nilai_grounding')->nullable();

            // Penerangan
            $table->boolean('check_kondisi_lampu')->nullable();
            $table->boolean('check_cover_lampu')->nullable();
            $table->boolean('check_wiring_penerangan')->nullable();
            $table->boolean('check_saklar')->nullable();
            $table->boolean('check_penyangga_penerangan')->nullable();

            // Sistem Distribusi
            $table->boolean('check_stecker')->nullable();
            $table->boolean('check_stop_kontak')->nullable();
            $table->boolean('check_terminal_listrik')->nullable();
            $table->boolean('check_pengabelan_distribusi')->nullable();
            $table->boolean('check_support_pelindung_distribusi')->nullable();

            // Capasitor Bank
            $table->boolean('check_kondisi_fisik_capacitor')->nullable();
            $table->boolean('check_nilai_farad')->nullable();
            $table->boolean('check_nilai_ampere')->nullable();
            $table->boolean('check_kebersihan_capacitor')->nullable();

            // Trafo
            $table->boolean('check_kebocoran_oli_sisi_bawah')->nullable();
            $table->boolean('check_kebocoran_oli_sisi_atas')->nullable();
            $table->boolean('check_level_oli')->nullable();

            // Optional catatan (biar konsisten seperti utility)
            // $table->text('keterangan')->nullable();
            // $table->string('korektif')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_electrical_inspections');
    }
};
