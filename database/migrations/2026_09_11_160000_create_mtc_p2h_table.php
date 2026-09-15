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
        Schema::create('mtc_p2h', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->foreignId('mesin_id')->nullable()->constrained('mtc_master_mesin')->nullOnDelete();
            $table->string('nomor_unit', 50)->index();
            $table->string('dept', 100)->nullable();
            $table->date('tanggal')->index();
            $table->string('shift', 20)->nullable();
            $table->string('jenis_p2h', 50)->index(); // Forklift, Pallet Mover
            $table->string('operator_name', 100)->nullable();
            $table->decimal('jam_operasional', 10, 2)->nullable();
            $table->decimal('persentase', 5, 2)->nullable();
            $table->string('status_kelayakan', 50)->nullable();
            $table->string('foto_kondisi_accu', 255)->nullable();
            $table->text('catatan')->nullable();

            // Field checklist sesuai API Warehouse (Forklift & Pallet Mover)
            $table->boolean('cek_baterai')->nullable();
            $table->boolean('cek_fork')->nullable();
            $table->boolean('kondisi_body_kebersihan')->nullable();
            $table->boolean('lampu_kiri')->nullable();
            $table->boolean('lampu_kanan')->nullable();
            $table->boolean('lampu_sorot')->nullable();
            $table->boolean('lampu_sign_depan_kanan')->nullable();
            $table->boolean('lampu_sign_depan_kiri')->nullable();
            $table->boolean('kipas_belakang')->nullable();
            $table->boolean('rantai_lift')->nullable();
            $table->boolean('sistem_hidrolik')->nullable();
            $table->boolean('kondisi_axle')->nullable();
            $table->boolean('sistem_kemudi')->nullable();
            $table->boolean('panel_display')->nullable();
            $table->boolean('air_aki')->nullable();
            $table->boolean('klakson')->nullable();
            $table->boolean('buzzer_mundur')->nullable();
            $table->boolean('kaca_spion')->nullable();
            $table->boolean('kondisi_ban')->nullable();
            $table->boolean('fungsi_rem')->nullable();
            $table->boolean('check_kunci_pm')->nullable();
            $table->boolean('check_kebersihan_unit')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_p2h');
    }
};
