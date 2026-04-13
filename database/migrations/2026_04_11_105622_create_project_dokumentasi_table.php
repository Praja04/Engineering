<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_dokumentasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                  ->constrained('project_masters')
                  ->cascadeOnDelete();
            $table->enum('fase', ['fase_1', 'fase_3']);
            // Dokumentasi hanya ada di Fase 1 dan Fase 3
            $table->enum('tipe', ['foto', 'dokumen']);
            // foto = file gambar (jpg, png, webp, dll)
            // dokumen = file dokumen (pdf, docx, xlsx, dll)
            $table->string('nama_file');
            // Nama file asli dari user
            $table->string('path_file');
            // Path penyimpanan di storage
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('ukuran_file')->nullable();
            // Ukuran file dalam bytes
            $table->foreignId('uploaded_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // User yang mengupload (dari tabel users existing)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_dokumentasi');
    }
};
