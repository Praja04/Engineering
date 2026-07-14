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
        Schema::create('agenda_tank_farm', function (Blueprint $table) {
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

        Schema::create('agenda_tank_farm_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_tank_farm_id')->nullable()->constrained('agenda_tank_farm')->nullOnDelete();
            $table->date('tanggal')->unique(); // Daily log is unique

            // 42 Checklist fields (all nullable strings)
            $table->string('kelistrikan_pompa_sumur_1', 10)->nullable();
            $table->string('kelistrikan_pompa_sumur_2', 10)->nullable();
            $table->string('kelistrikan_pompa_sumur_4', 10)->nullable();
            $table->string('kelistrikan_pompa_sumur_5', 10)->nullable();
            $table->string('pressure_pompa_sumur_1', 10)->nullable();
            $table->string('pressure_pompa_sumur_2', 10)->nullable();
            $table->string('pressure_pompa_sumur_4', 10)->nullable();
            $table->string('pressure_pompa_sumur_5', 10)->nullable();
            $table->string('flow_meter_pompa_sumur_1', 10)->nullable();
            $table->string('flow_meter_pompa_sumur_2', 10)->nullable();
            $table->string('flow_meter_pompa_sumur_4', 10)->nullable();
            $table->string('flow_meter_pompa_sumur_5', 10)->nullable();
            $table->string('drain_lumpur_settling_tank', 10)->nullable();
            $table->string('kelistrikan_pompa_10p3', 10)->nullable();
            $table->string('kelistrikan_pompa_10p3a', 10)->nullable();
            $table->string('pressure_gauge_intermediate', 10)->nullable();
            $table->string('level_bandul_tank_farm', 10)->nullable();
            $table->string('flow_meter_fresh_water_tank', 10)->nullable();
            $table->string('flow_meter_fwt_to_ro', 10)->nullable();
            $table->string('kelistrikan_pompa_10p4', 10)->nullable();
            $table->string('kelistrikan_pompa_10p4a', 10)->nullable();
            $table->string('pressure_gauge_pompa_10p4_p4a', 10)->nullable();
            $table->string('kelistrikan_pompa_10p5', 10)->nullable();
            $table->string('kelistrikan_pompa_10p5a', 10)->nullable();
            $table->string('kelistrikan_pompa_10p5b', 10)->nullable();
            $table->string('flow_meter_ro_reject_tank', 10)->nullable();
            $table->string('pressure_gauge_pompa_10p5_10p5a', 10)->nullable();
            $table->string('drain_lumpur_tangki_intermediate', 10)->nullable();
            $table->string('inspeksi_all_pompa_tf_intermediate', 10)->nullable();
            $table->string('inspeksi_pompa_20p1', 10)->nullable();
            $table->string('inspeksi_pompa_20p1a', 10)->nullable();
            $table->string('kelistrikan_pompa_20p2', 10)->nullable();
            $table->string('kelistrikan_pompa_20p2a', 10)->nullable();
            $table->string('kelistrikan_pompa_60p1', 10)->nullable();
            $table->string('kelistrikan_pompa_60p2', 10)->nullable();
            $table->string('kelistrikan_pompa_60p3', 10)->nullable();
            $table->string('pressure_gauge_pompa_60p1', 10)->nullable();
            $table->string('pressure_gauge_pompa_60p2', 10)->nullable();
            $table->string('pressure_gauge_pompa_60p3', 10)->nullable();
            $table->string('baterai_pompa_60p3', 10)->nullable();
            $table->string('bahan_bakar_pompa_60p3', 10)->nullable();
            $table->string('pressure_gauge_water_tank_hydrant', 10)->nullable();

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
        Schema::dropIfExists('agenda_tank_farm_details');
        Schema::dropIfExists('agenda_tank_farm');
    }
};
