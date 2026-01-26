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
        Schema::create('wwtp_performance_ph_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('shift', ['shift1', 'shift2', 'shift3']);
            $table->float('equalisasi_1')->nullable();
            $table->float('equalisasi_2')->nullable();
            $table->float('netralisasi')->nullable();
            $table->float('sedimentasi_1')->nullable();
            $table->float('sedimentasi_2')->nullable();
            $table->float('outlet_anaerob')->nullable();
            $table->float('aerob')->nullable();
            $table->float('lumpur_aktif')->nullable();
            $table->float('clarifier_2')->nullable();
            $table->float('outlet')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwtp_performance_ph_harian');
    }
};
