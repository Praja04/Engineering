<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_masters', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_moc')->unique();
            // Nomor unik identitas project
            $table->string('ejo')->nullable();
            // EJO = nomor tiket pengerjaan (opsional)
            $table->string('deskripsi');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // Penanggung jawab project (dari tabel users existing)
            $table->text('keterangan')->nullable();
            $table->enum('fase_aktif', ['fase_1', 'fase_2', 'fase_3'])
                  ->default('fase_1');
            // Menandakan project sedang berada di fase berapa
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_masters');
    }
};
