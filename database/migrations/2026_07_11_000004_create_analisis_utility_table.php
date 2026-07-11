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
        Schema::create('analisis_utility', function (Blueprint $table) {
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

        Schema::create('analisis_utility_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisis_utility_id')->nullable()->constrained('analisis_utility', 'id', 'fk_analisis_util_details_main')->nullOnDelete();
            $table->date('tanggal')->unique(); // Daily log is unique

            // 37 Checklist fields (all nullable strings containing OK / NOK)
            // pH
            $table->string('ph_fw_storage', 10)->nullable();
            $table->string('ph_ws_storage', 10)->nullable();
            $table->string('ph_ro_storage', 10)->nullable();
            $table->string('ph_in_mmf', 10)->nullable();
            $table->string('ph_buffer_tank_ws', 10)->nullable();
            $table->string('ph_outlet_ws', 10)->nullable();
            $table->string('ph_menara_ws', 10)->nullable();
            $table->string('ph_depo_lt1', 10)->nullable();
            $table->string('ph_depo_lt2', 10)->nullable();
            $table->string('ph_cooling_tower', 10)->nullable();
            $table->string('ph_boiler', 10)->nullable();
            $table->string('ph_outlet_ws_2', 10)->nullable();

            // TDS
            $table->string('tds_fw_storage', 10)->nullable();
            $table->string('tds_ws_storage', 10)->nullable();
            $table->string('tds_ro_storage', 10)->nullable();
            $table->string('tds_in_mmf', 10)->nullable();
            $table->string('tds_out_ro', 10)->nullable();
            $table->string('tds_menara_ws', 10)->nullable();
            $table->string('tds_daily_tank_dissolver', 10)->nullable();
            $table->string('tds_depo_lt1', 10)->nullable();
            $table->string('tds_depo_lt2', 10)->nullable();
            $table->string('tds_cooling_tower', 10)->nullable();
            $table->string('tds_boiler', 10)->nullable();

            // Turbidity
            $table->string('turbidity_in_mmf', 10)->nullable();
            $table->string('turbidity_out_mmf', 10)->nullable();
            $table->string('turbidity_cooling_tower', 10)->nullable();

            // Chlorine
            $table->string('chlorine_mmf', 10)->nullable();
            $table->string('chlorine_menara', 10)->nullable();
            $table->string('chlorine_depo_lt1', 10)->nullable();
            $table->string('chlorine_depo_lt2', 10)->nullable();
            $table->string('chlorine_daily_tank_dissolver', 10)->nullable();

            // Hardness
            $table->string('hardness_inlet_ws', 10)->nullable();
            $table->string('hardness_outlet_ws', 10)->nullable();
            $table->string('hardness_ws_storage', 10)->nullable();
            $table->string('hardness_ct', 10)->nullable();
            $table->string('hardness_ro', 10)->nullable();
            $table->string('hardness_boiler', 10)->nullable();

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
        Schema::dropIfExists('analisis_utility_details');
        Schema::dropIfExists('analisis_utility');
    }
};
