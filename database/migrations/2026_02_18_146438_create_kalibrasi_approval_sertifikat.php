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
        Schema::create('cal_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sertifikat_id')->constrained('cal_sertifikat')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'read', 'approved', 'rejected'])->default('pending');
            $table->integer('level')->nullable();
            $table->string('role')->nullable();
            $table->dateTime('action_at')->nullable();
            $table->foreignId('action_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->text('catatan')->nullable();
            $table->text('ttd')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_approvals');
    }
};
