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
        //
        Schema::create('scoring_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_scoring_id')->constrained()->onDelete('cascade');
            $table->foreignId('part_id')->constrained()->onDelete('cascade');
            $table->enum('result', ['OK', 'NOT OK']);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('machine_scoring_id');
            $table->index('part_id');
            $table->index('result');

            // Unique constraint to prevent duplicate scoring for same part in same scoring session
            $table->unique(['machine_scoring_id', 'part_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
