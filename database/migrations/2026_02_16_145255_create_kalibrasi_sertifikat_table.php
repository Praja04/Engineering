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
        Schema::create('cal_sertifikat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalibrasi_id')->constrained('cal_main')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // requester
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'issued'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cal_sertifikat');
    }
};
