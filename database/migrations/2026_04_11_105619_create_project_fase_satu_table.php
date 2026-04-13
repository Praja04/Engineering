<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_fase_satu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                  ->constrained('project_masters')
                  ->cascadeOnDelete();
            $table->string('ejo')->nullable();
            // EJO opsional per fase
            $table->string('deskripsi');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // Penanggung jawab fase ini (dari tabel users existing)
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_fase_satu');
    }
};
