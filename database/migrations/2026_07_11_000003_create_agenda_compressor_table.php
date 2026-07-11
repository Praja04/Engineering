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
        Schema::create('agenda_compressor', function (Blueprint $table) {
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

        Schema::create('agenda_compressor_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_compressor_id')->nullable()->constrained('agenda_compressor', 'id', 'fk_agenda_comp_details_main')->nullOnDelete();
            $table->date('tanggal')->unique(); // Daily log is unique

            // 25 Checklist fields (all nullable strings containing OK / NOK)
            $table->string('pressure_aq55vsd', 10)->nullable();
            $table->string('running_hour_aq55vsd', 10)->nullable();
            $table->string('element_outlet_aq55vsd', 10)->nullable();
            $table->string('kelistrikan_aq55vsd', 10)->nullable();
            $table->string('rpm_aq55vsd', 10)->nullable();
            
            $table->string('pressure_ga37', 10)->nullable();
            $table->string('running_hour_ga37', 10)->nullable();
            $table->string('kelistrikan_ga37', 10)->nullable();
            $table->string('element_outlet_ga37', 10)->nullable();
            
            $table->string('pressure_ir55', 10)->nullable();
            $table->string('running_hour_ir55', 10)->nullable();
            $table->string('kelistrikan_ir55', 10)->nullable();
            $table->string('temperature_ir55', 10)->nullable();
            
            $table->string('cleaning_strainer_aq55vsd', 10)->nullable();
            $table->string('cleaning_valve_ga37', 10)->nullable();
            $table->string('replace_filter_ir55', 10)->nullable();
            
            $table->string('inspeksi_motor_aq55vsd', 10)->nullable();
            $table->string('inspeksi_motor_ga37', 10)->nullable();
            $table->string('inspeksi_motor_ir55', 10)->nullable();
            
            $table->string('inspeksi_dryer_120', 10)->nullable();
            $table->string('inspeksi_dryer_tr15', 10)->nullable();
            $table->string('inspeksi_dryer_ir', 10)->nullable();
            
            $table->string('pressure_in_out_ct', 10)->nullable();
            $table->string('pressure_bejana_receiver', 10)->nullable();
            $table->string('pressure_in_out_dryer', 10)->nullable();

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
        Schema::dropIfExists('agenda_compressor_details');
        Schema::dropIfExists('agenda_compressor');
    }
};
