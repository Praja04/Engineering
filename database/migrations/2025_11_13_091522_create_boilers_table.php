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
        Schema::create('boilers', function (Blueprint $table) {
            $table->id();
            $table->enum('periode_tipe', ['weekly', 'monthly']);
            $table->integer('week')->nullable();
            $table->date('tanggal');
            $table->decimal('batu_bara', 10, 3); // ton
            $table->decimal('steam', 10, 3);     // m³
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boilers');
    }
};
