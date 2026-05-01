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
        Schema::create('warming_up_gensets', function (Blueprint $table) {
            $table->id();

            // Operator input
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Penunjukan approver (dipilih saat input)
            $table->foreignId('foreman_id')->nullable()->constrained('users');
            $table->foreignId('supervisor_id')->nullable()->constrained('users');

            // Tanggal laporan
            $table->date('tanggal_laporan');

            // Input data
            $table->time('jam_pencatatan')->nullable();
            $table->decimal('engine_speed', 10, 2)->nullable();
            $table->decimal('engine_temperature', 10, 2)->nullable();
            $table->decimal('engine_oil_pressure', 10, 2)->nullable();
            $table->decimal('battery_voltage', 10, 2)->nullable();
            $table->decimal('charge_alt_voltage', 10, 2)->nullable();
            $table->decimal('running_hour', 10, 2)->nullable();
            $table->decimal('frequency', 10, 2)->nullable();
            $table->decimal('status_oil', 10, 2)->nullable();
            $table->decimal('status_bbm', 10, 2)->nullable();

            // Status workflow
            $table->enum('status', [
                'draft',
                'submitted',
                'approved_foreman',
                'approved_supervisor',
                'rejected'
            ])->default('draft');

            // Approval action
            $table->foreignId('approved_foreman_by')->nullable()->constrained('users');
            $table->timestamp('approved_foreman_at')->nullable();

            $table->foreignId('approved_supervisor_by')->nullable()->constrained('users');
            $table->timestamp('approved_supervisor_at')->nullable();

            $table->text('reject_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warming_up_gensets');
    }
};
