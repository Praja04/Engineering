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
        Schema::create(
            'compressor',
            function (Blueprint $table) {
                $table->id();
                $table->date('tgl_awal');
                $table->date('tgl_akhir');
                $table->unsignedTinyInteger('week');  // 1-4
                $table->unsignedTinyInteger('bulan');  // 1-12
                $table->unsignedSmallInteger('tahun'); // e.g. 2025
                $table->enum('status', ['draft', 'submitted', 'approved_foreman', 'approved_supervisor', 'rejected'])->default('draft');
                $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('foreman_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('approved_foreman_at')->nullable();
                $table->timestamp('approved_supervisor_at')->nullable();
                $table->text('reject_reason')->nullable();
                $table->timestamps();
            }
        );

        Schema::create('compressor_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compressor_id')->nullable()->constrained('compressor')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam');

            // pressure
            $table->decimal('pressure_outlet_1', 10, 2)->nullable();
            $table->decimal('pressure_outlet_2', 10, 2)->nullable();
            $table->decimal('pressure_outlet_3', 10, 2)->nullable();
            $table->decimal('pressure_outlet_4', 10, 2)->nullable();

            // element
            $table->decimal('element_outlet_1', 10, 2)->nullable();
            $table->decimal('element_outlet_2', 10, 2)->nullable();
            $table->decimal('element_outlet_4', 10, 2)->nullable();

            $table->decimal('load_percent', 10, 2)->nullable();

            // running hour
            $table->decimal('running_hour_1', 10, 2)->nullable();
            $table->decimal('running_hour_2', 10, 2)->nullable();
            $table->decimal('running_hour_3', 10, 2)->nullable();
            $table->decimal('running_hour_4', 10, 2)->nullable();

            // loaded hour
            $table->decimal('loaded_hour_1', 10, 2)->nullable();
            $table->decimal('loaded_hour_2', 10, 2)->nullable();
            $table->decimal('loaded_hour_3', 10, 2)->nullable();
            $table->decimal('loaded_hour_4', 10, 2)->nullable();

            // motor start
            $table->decimal('motor_start_1', 10, 2)->nullable();
            $table->decimal('motor_start_2', 10, 2)->nullable();
            $table->decimal('motor_start_3', 10, 2)->nullable();
            $table->decimal('motor_start_4', 10, 2)->nullable();

            $table->decimal('accumulated_volume', 10, 2)->nullable();
            $table->decimal('temperature_comp_ir', 10, 2)->nullable();
            $table->decimal('pressure_in', 10, 2)->nullable();
            $table->decimal('pressure_out', 10, 2)->nullable();
            $table->decimal('suhu_dryer_tr15', 10, 2)->nullable();
            $table->decimal('suhu_dryer_fx250', 10, 2)->nullable();
            $table->decimal('suhu_dryer_ir', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compressor_details');
        Schema::dropIfExists('compressor');
    }
};
