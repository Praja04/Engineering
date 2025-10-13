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
        Schema::create('chemical_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chemical_area_id')->default(0);
            $table->string('nama_chemical', 50)->default('0');
            $table->string('satuan', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            // Jika nanti ingin pakai relasi:
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chemical_types');
    }
};
