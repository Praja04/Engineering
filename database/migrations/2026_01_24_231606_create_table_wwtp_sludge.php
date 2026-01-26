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
        Schema::create('wwtp_sludge', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('shift', ['shift1', 'shift2', 'shift3']);
            $table->float('drain_lumpur')->nullable();
            $table->float('running_hour_scp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwtp_sludge');
    }
};
