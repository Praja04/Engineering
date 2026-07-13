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

            // 37 input decimal
            $table->decimal('ph_fw_storage', 10, 2)->nullable();
            $table->decimal('ph_ws_storage', 10, 2)->nullable();
            $table->decimal('ph_ro_storage', 10, 2)->nullable();
            $table->decimal('ph_in_mmf', 10, 2)->nullable();
            $table->decimal('ph_buffer_tank_ws', 10, 2)->nullable();
            $table->decimal('ph_outlet_ws', 10, 2)->nullable();
            $table->decimal('ph_menara_ws', 10, 2)->nullable();
            $table->decimal('ph_depo_lt1', 10, 2)->nullable();
            $table->decimal('ph_depo_lt2', 10, 2)->nullable();
            $table->decimal('ph_cooling_tower', 10, 2)->nullable();
            $table->decimal('ph_boiler', 10, 2)->nullable();
            $table->decimal('ph_outlet_ws_2', 10, 2)->nullable();

            // TDS
            $table->decimal('tds_fw_storage', 10, 2)->nullable();
            $table->decimal('tds_ws_storage', 10, 2)->nullable();
            $table->decimal('tds_ro_storage', 10, 2)->nullable();
            $table->decimal('tds_in_mmf', 10, 2)->nullable();
            $table->decimal('tds_out_ro', 10, 2)->nullable();
            $table->decimal('tds_menara_ws', 10, 2)->nullable();
            $table->decimal('tds_daily_tank_dissolver', 10, 2)->nullable();
            $table->decimal('tds_depo_lt1', 10, 2)->nullable();
            $table->decimal('tds_depo_lt2', 10, 2)->nullable();
            $table->decimal('tds_cooling_tower', 10, 2)->nullable();
            $table->decimal('tds_boiler', 10, 2)->nullable();

            // Turbidity
            $table->decimal('turbidity_in_mmf', 10, 2)->nullable();
            $table->decimal('turbidity_out_mmf', 10, 2)->nullable();
            $table->decimal('turbidity_cooling_tower', 10, 2)->nullable();

            // Chlorine
            $table->decimal('chlorine_mmf', 10, 2)->nullable();
            $table->decimal('chlorine_menara', 10, 2)->nullable();
            $table->decimal('chlorine_depo_lt1', 10, 2)->nullable();
            $table->decimal('chlorine_depo_lt2', 10, 2)->nullable();
            $table->decimal('chlorine_daily_tank_dissolver', 10, 2)->nullable();

            // Hardness
            $table->decimal('hardness_inlet_ws', 10, 2)->nullable();
            $table->decimal('hardness_outlet_ws', 10, 2)->nullable();
            $table->decimal('hardness_ws_storage', 10, 2)->nullable();
            $table->decimal('hardness_ct', 10, 2)->nullable();
            $table->decimal('hardness_ro', 10, 2)->nullable();
            $table->decimal('hardness_boiler', 10, 2)->nullable();

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
