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
        Schema::create('boiler_approvals', function (Blueprint $table) {
            $table->id();

            // Tanggal approval (1 hari cuma 1 approval)
            $table->date('tanggal')->unique();

            $table->foreignId('foreman_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('status', [
                'draft',
                'waiting_supervisor',
                'approved_supervisor'
            ])->default('draft');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boiler_approvals');
    }
};
