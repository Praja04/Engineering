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
        Schema::create('mtc_main', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_mtc')->nullable();
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('paket')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('korektif')->nullable();
            $table->string('area')->nullable();
            $table->string('departemen')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->integer('runnning_hour')->nullable();
            $table->enum('status', ['pending', 'waiting', 'approved', 'rejected'])->default('pending');
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
        Schema::dropIfExists('mtc_main');
    }
};
