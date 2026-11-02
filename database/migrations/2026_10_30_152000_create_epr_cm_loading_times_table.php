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
        Schema::create('epr_cm_loading_times', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('mesin');
            $table->string('kategori')->default('Pouch'); // Pouch or Sachet
            $table->integer('loading_time_a')->nullable()->default(0);
            $table->integer('loading_time_b')->nullable()->default(0);
            $table->integer('loading_time_c')->nullable()->default(0);
            $table->integer('loading_time_all')->nullable()->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['tanggal', 'mesin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epr_cm_loading_times');
    }
};

