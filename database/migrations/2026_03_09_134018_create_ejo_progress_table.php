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
        Schema::create('ejo_progress', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ejo_id')
            ->constrained('ejo_tickets')
            ->cascadeOnDelete();

            $table->integer('progress_percent')->default(0);

            $table->text('progress_note')->nullable();

            $table->foreignId('updated_by')
            ->constrained('users')
            ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejo_progress');
    }
};
