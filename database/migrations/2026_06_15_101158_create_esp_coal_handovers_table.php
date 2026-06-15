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
        Schema::create('esp_coal_handovers', function (Blueprint $table) {
            $table->id();
            
            // Tanggal serah terima
            $table->date('tanggal_laporan');

            // Penyuplai (Warehouse)
            $table->decimal('penyuplai_qty', 10, 2);
            $table->string('penyuplai_nik_nama');

            // Penerima (ENG)
            $table->decimal('penerima_qty', 10, 2);
            $table->string('penerima_nik_nama');

            // Operator input
            $table->foreignId('operator_id')->constrained('users');

            $table->timestamps();

            // Mencegah data ganda pada tanggal yang sama
            $table->unique('tanggal_laporan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('esp_coal_handovers');
    }
};
