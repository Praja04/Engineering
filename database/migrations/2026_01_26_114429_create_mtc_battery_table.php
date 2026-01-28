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
        Schema::create('mtc_battery', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('battery_type')->nullable();
            $table->string('no_seri')->nullable();
            $table->string('no_unit')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });

        Schema::create('mtc_battery_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('battery_id')->constrained('mtc_battery')->onDelete('cascade');
            $table->boolean('voltase')->nullable();
            $table->boolean('level_air_aki')->nullable();
            $table->boolean('intercell')->nullable();
            $table->boolean('kondisi_skun')->nullable();
            $table->boolean('kondisi_unit')->nullable();
            $table->boolean('grounding')->nullable();
            $table->integer('cell');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_battery_detail');
        Schema::dropIfExists('mtc_battery');
    }
};
