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
        Schema::create('mtc_master_mesin', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_mtc');
            $table->string('nama_mesin');
            $table->string('lokasi');
            $table->string('frekuensi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_master_mesin');
    }
};
