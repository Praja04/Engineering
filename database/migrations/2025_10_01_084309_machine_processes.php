<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the 'machine_processes' table,
 * corresponding to the MachineProcess Model.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('machine_processes', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Keys (Relationships based on $fillable)
            // Relasi ke tabel 'machines'
            $table->foreignId('machine_id')->constrained('machines')->onDelete('cascade');

            // Relasi ke tabel 'process_parameters'
            $table->foreignId('process_parameter_id')->constrained('process_parameters')->onDelete('cascade');

            // Data Fields based on $fillable
            // Menggunakan tipe data 'text' untuk kolom catatan
            $table->text('catatan')->nullable();

            // Timestamps (created_at and updated_at)
            $table->timestamps();

            // Optional: Index for common query performance
            $table->index(['machine_id', 'process_parameter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_processes');
    }
};
