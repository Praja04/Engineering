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
        Schema::create('mdp_monitorings', function (Blueprint $table) {
            $table->id();

            // Operator input
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Penunjukan approver (dipilih saat input)
            $table->foreignId('foreman_id')->nullable()->constrained('users');
            $table->foreignId('supervisor_id')->nullable()->constrained('users');

            // Tanggal laporan
            $table->date('tanggal_laporan');
            $table->time('jam_pencatatan');

            // Input data
            $table->decimal('e_del', 15, 2)->nullable();
            $table->decimal('arus_rata_rata', 10, 2)->nullable();
            $table->decimal('arus_i1', 10, 2)->nullable();
            $table->decimal('arus_i2', 10, 2)->nullable();
            $table->decimal('arus_i3', 10, 2)->nullable();
            $table->decimal('tegangan_rata_rata', 10, 2)->nullable();
            $table->decimal('tegangan_v1', 10, 2)->nullable();
            $table->decimal('tegangan_v2', 10, 2)->nullable();
            $table->decimal('tegangan_v3', 10, 2)->nullable();
            $table->decimal('daya_total', 15, 2)->nullable();
            $table->decimal('daya_p1', 10, 2)->nullable();
            $table->decimal('daya_p2', 10, 2)->nullable();
            $table->decimal('daya_p3', 10, 2)->nullable();
            $table->decimal('temperatur_transformator', 10, 2)->nullable();
            $table->enum('level_oil', ['ok', 'nok'])->nullable();

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
        Schema::dropIfExists('mdp_monitorings');
    }
};
