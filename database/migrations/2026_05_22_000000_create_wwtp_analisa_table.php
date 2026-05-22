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
        Schema::create('wwtp_analisa', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->float('cod')->nullable();
            $table->float('tss')->nullable();
            $table->float('ph')->nullable();
            $table->float('ec')->nullable();
            $table->float('do')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwtp_analisa');
    }
};
