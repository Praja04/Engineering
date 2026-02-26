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
        Schema::create('wwtp_performance_sample', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('jenis_sampel');
            $table->integer('id_sampel');
            $table->decimal('tss', 10, 2)->default(0);
            $table->decimal('sv30', 10, 2)->default(0);
            $table->decimal('ph', 10, 2)->default(0);
            $table->decimal('mlss', 10, 2)->default(0);
            $table->decimal('svl', 10, 2)->default(0);
            $table->decimal('do', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwtp_performance_sample');
    }
};
