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
        Schema::create('mtc_agenda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mtc_master_mesin')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->unsignedTinyInteger('minggu_ke'); // 1-5
            $table->string('paket', 50); // e.g. A, B, C, D, Z
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['mesin_id', 'tahun', 'bulan', 'minggu_ke'], 'mtc_agenda_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_agenda');
    }
};
