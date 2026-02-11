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
        Schema::create('mtc_battery_main', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->onDelete('cascade');
            $table->string('battery_type')->nullable();
            $table->string('no_seri')->nullable();
            $table->string('no_unit')->nullable();
            $table->string('kondisi_plug_battery')->nullable();
            $table->decimal('total_voltase', 10, 2)->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('mtc_battery_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mtc_battery_id')->constrained('mtc_battery_main')->onDelete('cascade');
            $table->decimal('voltase', 10, 2)->nullable();
            $table->boolean('level_air_aki')->nullable();
            $table->boolean('intercell')->nullable();
            $table->boolean('kondisi_skun')->nullable();
            $table->boolean('kondisi_unit')->nullable();
            $table->decimal('grounding', 10, 2)->nullable();
            $table->integer('cell');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_battery_inspections');
        Schema::dropIfExists('mtc_battery_main');
    }
};
