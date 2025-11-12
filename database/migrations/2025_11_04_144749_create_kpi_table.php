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
        Schema::create('kpi', function (Blueprint $table) {
            $table->id();
            $table->enum('periode_tipe', ['weekly', 'monthly']);
            $table->date('tanggal');
            $table->decimal('fg', 10, 2); // ton
            $table->decimal('kecap_matang', 10, 2);  // ton
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi');
    }
};
