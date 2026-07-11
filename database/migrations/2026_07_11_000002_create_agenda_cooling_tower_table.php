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
        Schema::create('agenda_cooling_tower', function (Blueprint $table) {
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

        Schema::create('agenda_cooling_tower_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_cooling_tower_id')->nullable()->constrained('agenda_cooling_tower', 'id', 'fk_agenda_ct_details_main')->nullOnDelete();
            $table->date('tanggal')->unique(); // Daily log is unique

            // 36 Checklist fields (all nullable strings containing OK / NOK)
            $table->string('kelistrikan_pompa_10000p2', 10)->nullable();
            $table->string('kelistrikan_pompa_10000p2a', 10)->nullable();
            $table->string('kelistrikan_pompa_10000p2b', 10)->nullable();
            $table->string('kelistrikan_fan_1', 10)->nullable();
            $table->string('kelistrikan_fan_2', 10)->nullable();
            $table->string('kelistrikan_fan_3', 10)->nullable();
            $table->string('kelistrikan_fan_4', 10)->nullable();
            
            $table->string('suhu_out_ct', 10)->nullable();
            $table->string('suhu_in_ct', 10)->nullable();
            $table->string('pressure_out_ct', 10)->nullable();
            $table->string('pressure_in_ct', 10)->nullable();
            $table->string('ph_air_ct', 10)->nullable();
            $table->string('stok_chemical', 10)->nullable();
            
            $table->string('cleaning_saringan_bak', 10)->nullable();
            $table->string('cleaning_strainer_10000p2', 10)->nullable();
            $table->string('cleaning_strainer_10000p2a', 10)->nullable();
            $table->string('cleaning_strainer_10000p2b', 10)->nullable();
            
            $table->string('greasing_pompa_10000p2', 10)->nullable();
            $table->string('greasing_pompa_10000p2a', 10)->nullable();
            $table->string('greasing_pompa_10000p2b', 10)->nullable();
            
            $table->string('rubber_coupling_10000p2', 10)->nullable();
            $table->string('rubber_coupling_10000p2a', 10)->nullable();
            $table->string('rubber_coupling_10000p2b', 10)->nullable();
            
            $table->string('cleaning_valve_10000p2', 10)->nullable();
            $table->string('cleaning_valve_10000p2a', 10)->nullable();
            $table->string('cleaning_valve_10000p2b', 10)->nullable();
            
            $table->string('kalibrasi_dosis_chemical', 10)->nullable();
            
            $table->string('greasing_cleaning_fan_1', 10)->nullable();
            $table->string('greasing_cleaning_fan_2', 10)->nullable();
            $table->string('greasing_cleaning_fan_3', 10)->nullable();
            $table->string('greasing_cleaning_fan_4', 10)->nullable();
            
            $table->string('sling_fan_ct_1', 10)->nullable();
            $table->string('sling_fan_ct_2', 10)->nullable();
            $table->string('sling_fan_ct_3', 10)->nullable();
            $table->string('sling_fan_ct_4', 10)->nullable();
            
            $table->string('inspeksi_baut_mur', 10)->nullable();

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
        Schema::dropIfExists('agenda_cooling_tower_details');
        Schema::dropIfExists('agenda_cooling_tower');
    }
};
