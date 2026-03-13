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
        Schema::create('ejo_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ejo_id')
            ->constrained('ejo_tickets')
            ->cascadeOnDelete();

            $table->text('note');

            $table->foreignId('user_id')
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
        Schema::dropIfExists('ejo_notes');
    }
};
