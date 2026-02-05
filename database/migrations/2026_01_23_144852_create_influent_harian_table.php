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
        Schema::create('wwtp_influent_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('shift', ['shift1', 'shift2', 'shift3']);
            $table->decimal('pit_sparta', 10, 2)->default(0);
            $table->decimal('pit_garam', 10, 2)->default(0);
            $table->decimal('pit_domestik', 10, 2)->default(0);
            $table->decimal('pit_produksi_step3', 15, 2)->default(0);
            $table->decimal('pit_storage', 15, 2)->default(0);
            $table->decimal('pit_proses_wwtp2', 15, 2)->default(0);
            $table->decimal('pit_outlet', 15, 2)->default(0);
            $table->decimal('pit_boiler', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwtp_influent_harian');
    }
};
