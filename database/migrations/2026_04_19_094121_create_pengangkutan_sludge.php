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
        Schema::create('pengangkutan_sludge', function (Blueprint $table) {
            $table->id();
            $table->date('week_start'); // tanggal mulai minggu
            $table->date('week_end');   // tanggal akhir minggu
            $table->float('jumlah_pengangkutan'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengangkutan_sludge');
    }
};
