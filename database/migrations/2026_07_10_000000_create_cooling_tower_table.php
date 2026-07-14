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
        Schema::create('cooling_tower', function (Blueprint $table) {
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

        Schema::create('cooling_tower_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooling_tower_id')->nullable()->constrained('cooling_tower')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam');

            // data teknis cooling tower
            $table->decimal('pressure_ct_in', 10, 2)->nullable();
            $table->decimal('pressure_ct_out', 10, 2)->nullable();
            $table->decimal('temp_ct_in', 10, 2)->nullable();
            $table->decimal('temp_ct_out', 10, 2)->nullable();
            $table->decimal('flowrate_ro_awal', 10, 2)->nullable();
            $table->decimal('flowrate_ro_akhir', 10, 2)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Constraint: no duplicate date and hour
            $table->unique(['tanggal', 'jam']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooling_tower_details');
        Schema::dropIfExists('cooling_tower');
    }
};
