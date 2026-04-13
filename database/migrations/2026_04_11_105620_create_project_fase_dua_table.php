<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_fase_dua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                  ->constrained('project_masters')
                  ->cascadeOnDelete();
            $table->string('ejo')->nullable();
            $table->string('deskripsi');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // Penanggung jawab fase ini (dari tabel users existing)
            $table->string('nomor_io');
            // Nomor IO untuk kebutuhan pengadaan
            $table->text('keterangan')->nullable();

            // Persentase status pengadaan (0 - 100)
            $table->unsignedTinyInteger('persen_pr')->default(0);
            // PR = Purchase Request
            $table->unsignedTinyInteger('persen_po')->default(0);
            // PO = Purchase Order
            $table->unsignedTinyInteger('persen_gr')->default(0);
            // GR = Goods Receipt

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_fase_dua');
    }
};
