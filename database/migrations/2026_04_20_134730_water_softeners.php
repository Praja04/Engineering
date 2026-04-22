<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_softeners', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');

            // WS 1
            $table->time('ws1_jam')->nullable();
            $table->decimal('ws1_hardness_in', 8, 2)->nullable();   // ppm
            $table->decimal('ws1_hardness_out', 8, 2)->nullable();  // ppm
            $table->decimal('ws1_flow', 8, 2)->nullable();          // m3/h

            // WS 2
            $table->time('ws2_jam')->nullable();
            $table->decimal('ws2_hardness_in', 8, 2)->nullable();   // ppm
            $table->decimal('ws2_hardness_out', 8, 2)->nullable();  // ppm
            $table->decimal('ws2_flow', 8, 2)->nullable();          // m3/h

            // Regen 1
            $table->time('regen1_jam')->nullable();
            $table->decimal('regen1_air_pelarut', 8, 2)->nullable(); // m3
            $table->decimal('regen1_garam', 8, 2)->nullable();       // kg
            $table->unsignedTinyInteger('regen1_nomer_ws')->nullable();

            // Regen 2
            $table->time('regen2_jam')->nullable();
            $table->decimal('regen2_air_pelarut', 8, 2)->nullable(); // m3
            $table->decimal('regen2_garam', 8, 2)->nullable();       // kg
            $table->unsignedTinyInteger('regen2_nomer_ws')->nullable();

            $table->timestamps();

            $table->unique('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_softeners');
    }
};
