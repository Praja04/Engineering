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
        Schema::create('wwtp_performance_weeks', function (Blueprint $table) {
            $table->id();
            $table->date('week_start'); // tanggal mulai minggu
            $table->date('week_end');   // tanggal akhir minggu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wwtp_performance_weeks');
    }
};
