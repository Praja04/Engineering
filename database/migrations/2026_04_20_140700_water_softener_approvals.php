<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_softener_approvals', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('bulan');  // 1-12
            $table->unsignedSmallInteger('tahun'); // e.g. 2025

            $table->foreignId('operator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('foreman_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('supervisor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', [
                'draft',               // Operator masih input
                'waiting_foreman',     // Bulan penuh, siap di-approve foreman
                'approved_foreman',    // Foreman sudah approve, notif ke supervisor
                'approved_supervisor', // Final
            ])->default('draft');

            $table->timestamp('submitted_at')->nullable();         // Saat bulan penuh & submit
            $table->timestamp('foreman_approved_at')->nullable();
            $table->timestamp('supervisor_approved_at')->nullable();

            $table->timestamps();

            $table->unique(['bulan', 'tahun']); // 1 record per bulan per tahun
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_softener_approvals');
    }
};
