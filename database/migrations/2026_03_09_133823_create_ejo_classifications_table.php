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
        Schema::create('ejo_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')
            ->constrained('ejo_types')
            ->cascadeOnDelete();

            $table->string('name'); // sipil, mekanik, repair part, dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejo_classifications');
    }
};
