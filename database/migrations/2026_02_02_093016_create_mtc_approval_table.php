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
        Schema::create('mtc_approval', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->onDelete('cascade');
            $table->integer('level')->nullable();
            $table->string('role')->nullable();
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'read', 'approved', 'rejected'])->default('pending');
            $table->dateTime('action_at')->nullable();
            $table->foreignId('action_by')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('mtc_approval');
    }
};
