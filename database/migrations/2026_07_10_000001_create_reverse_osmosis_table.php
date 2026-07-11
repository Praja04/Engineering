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
        Schema::create('reverse_osmosis', function (Blueprint $table) {
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

        Schema::create('reverse_osmosis_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reverse_osmosis_id')->nullable()->constrained('reverse_osmosis')->nullOnDelete();
            $table->date('tanggal')->unique(); // Daily log is unique

            // MMF (B - I)
            $table->decimal('mmf_pressure_feed_1', 10, 2)->nullable();
            $table->decimal('mmf_pressure_feed_2', 10, 2)->nullable();
            $table->decimal('mmf_pressure_produk_1', 10, 2)->nullable();
            $table->decimal('mmf_pressure_produk_2', 10, 2)->nullable();
            $table->decimal('mmf_output_flow_1', 10, 2)->nullable();
            $table->decimal('mmf_output_flow_2', 10, 2)->nullable();
            $table->boolean('mmf_status_backwash_1')->default(false);
            $table->boolean('mmf_status_backwash_2')->default(false);

            // Micron Filter (J - K)
            $table->decimal('micron_filter_pressure_inlet', 10, 2)->nullable();
            $table->decimal('micron_filter_pressure_outlet', 10, 2)->nullable();

            // RO (L - S)
            $table->decimal('ro_permeate_flowrate', 10, 2)->nullable();
            $table->decimal('ro_reject_flowrate', 10, 2)->nullable();
            $table->decimal('ro_flowmeter_accumulation', 10, 2)->nullable();
            $table->decimal('ro_pressure_inlet_1st_stage', 10, 2)->nullable();
            $table->decimal('ro_pressure_inlet_2nd_stage', 10, 2)->nullable();
            $table->decimal('ro_pressure_concentrate', 10, 2)->nullable();
            $table->decimal('ro_pressure_produk', 10, 2)->nullable();

            // CIP (T - W)
            $table->string('cip_keterangan')->nullable();
            $table->string('cip_jenis_chemical')->nullable();
            $table->string('cip_qty_chemical')->nullable();
            $table->string('cip_hasil')->nullable();

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
        Schema::dropIfExists('reverse_osmosis_details');
        Schema::dropIfExists('reverse_osmosis');
    }
};
