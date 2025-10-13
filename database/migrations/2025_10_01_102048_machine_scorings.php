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
        Schema::create('machine_scorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_process_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('scoring_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('machine_process_id');
            $table->index('user_id');
            $table->index('scoring_date');
            $table->index('status');
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
