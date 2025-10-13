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
        Schema::create('kalibrasi_sertifikat_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sertifikat_id')->constrained('kalibrasi_sertifikat')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('approver_email');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('comment')->nullable();     // alasan approve/reject
            $table->timestamp('approved_at')->nullable(); // kapan action dilakukan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasi_sertifikat_approvals');
    }
};
