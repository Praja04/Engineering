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
        Schema::create('boilers', function (Blueprint $table) {
            $table->id();
            // $table->enum('periode_tipe', ['weekly', 'monthly']);
            $table->date('date')->nullable();
            // $table->date('end_date')->nullable();
            // $table->string('month', 7)->nullable();
            $table->decimal('batu_bara', 10, 3); // ton
            $table->decimal('steam', 10, 3);     // M3
            $table->decimal('kondensat', 10, 3);     // %
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boilers');
    }
};
