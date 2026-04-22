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
        //
        Schema::create('capacitor_bank_approvals', function (Blueprint $table) {
            $table->id();

            $table->integer('bulan');
            $table->integer('tahun');

            $table->foreignId('operator_id')->nullable()->constrained('users');
            $table->foreignId('foreman_id')->nullable()->constrained('users');
            $table->foreignId('supervisor_id')->nullable()->constrained('users');

            $table->string('status')->default('draft');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('foreman_approved_at')->nullable();
            $table->timestamp('supervisor_approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('capasitor_bank_approvals');
    }
};
