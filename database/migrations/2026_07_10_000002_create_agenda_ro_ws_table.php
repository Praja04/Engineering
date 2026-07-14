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
        Schema::create('agenda_ro_ws', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');  // 1-12
            $table->unsignedSmallInteger('tahun'); // e.g. 2026
            $table->enum('status', ['draft', 'submitted', 'approved_foreman', 'approved_supervisor', 'rejected'])->default('draft');
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('foreman_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_foreman_at')->nullable();
            $table->timestamp('approved_supervisor_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('agenda_ro_ws_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_ro_ws_id')->nullable()->constrained('agenda_ro_ws')->nullOnDelete();
            $table->date('tanggal')->unique(); // Daily log is unique

            // Checklist fields (Inspeksi 1 - 19)
            $table->string('inspeksi_hpt_pump', 10)->nullable();
            $table->string('inspeksi_cip_pump', 10)->nullable();
            $table->string('inspeksi_blower_ro', 10)->nullable();
            $table->string('cek_chemical', 10)->nullable();
            $table->string('pencatatan_flow_meter_produksi', 10)->nullable();
            $table->string('cek_nilai_conductivity', 10)->nullable();
            $table->string('cek_dp_1st_2st', 10)->nullable();
            $table->string('cek_dp_mmf_1_2', 10)->nullable();
            $table->string('pencatatan_flow_meter_konsumsi', 10)->nullable();
            $table->string('backwash_mmf_1', 10)->nullable();
            $table->string('backwash_mmf_2', 10)->nullable();
            $table->string('cek_kondisi_rotameter_mmf_1', 10)->nullable();
            $table->string('cek_kondisi_rotameter_mmf_2', 10)->nullable();
            $table->string('cek_kondisi_rotameter_ro_product', 10)->nullable();
            $table->string('cek_kondisi_rotameter_ro_reject', 10)->nullable();
            $table->string('kalibrasi_dosis_kimia', 10)->nullable();
            $table->string('cleaning_unit_ro', 10)->nullable();
            $table->string('cleaning_unit_mmf_1', 10)->nullable();
            $table->string('cleaning_unit_mmf_2', 10)->nullable();

            // Checklist fields (Inspeksi 20 - 25)
            $table->string('cek_output_hardness', 10)->nullable();
            $table->string('cek_flow_produk', 10)->nullable();
            $table->string('regenerasi_mesin_ws', 10)->nullable();
            $table->string('cek_pompa_transfer', 10)->nullable();
            $table->string('cek_pompa_suplai', 10)->nullable();
            $table->string('cleaning_tanki_buffer_ws', 10)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_ro_ws_details');
        Schema::dropIfExists('agenda_ro_ws');
    }
};
