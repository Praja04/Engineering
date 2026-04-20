<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('esp_shift_reports', function (Blueprint $table) {
            $table->id();

            // Tanggal laporan shift
            $table->date('tanggal_laporan');

            // Data operasional
            $table->decimal('pemakaian_air', 10, 2)->nullable();      // m3
            $table->decimal('pemakaian_steam', 10, 2)->nullable();    // ton
            $table->decimal('pemakaian_batubara', 10, 2)->nullable(); // ton
            $table->decimal('efisiensi_batubara', 10, 2)->nullable(); // kg/m3

            $table->decimal('running_hour_awal', 10, 2)->nullable();
            $table->decimal('running_hour_akhir', 10, 2)->nullable();

            $table->decimal('feed_tank_awal', 10, 2)->nullable();
            $table->decimal('feed_tank_akhir', 10, 2)->nullable();

            $table->decimal('pengisian_batubara', 10, 2)->nullable(); // bucket
            $table->decimal('chemical_scf', 10, 2)->nullable();       // liter
            $table->decimal('chemical_srtf', 10, 2)->nullable();      // liter
            $table->decimal('dosis', 10, 2)->nullable();              // ml/min

            // USER INPUT
            $table->foreignId('operator_id')->constrained('users');

            // APPROVAL
            $table->foreignId('foreman_id')->nullable()->constrained('users');
            $table->timestamp('foreman_approved_at')->nullable();

            $table->foreignId('supervisor_id')->nullable()->constrained('users');
            $table->timestamp('supervisor_approved_at')->nullable();

            // STATUS
            $table->enum('status', ['draft', 'approved_operator', 'approved_foreman', 'approved_supervisor'])
            ->default('draft');

            $table->timestamps();

            // 1 hari cuma 1 laporan
            $table->unique('tanggal_laporan');
        });
    }
};
