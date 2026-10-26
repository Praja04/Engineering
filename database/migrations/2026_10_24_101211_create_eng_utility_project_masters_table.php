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
        Schema::create('eng_utility_project_areas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_area', 100)->unique();
            $table->string('kode_area', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('eng_utility_project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 150)->unique();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eng_utility_project_categories');
        Schema::dropIfExists('eng_utility_project_areas');
    }
};
