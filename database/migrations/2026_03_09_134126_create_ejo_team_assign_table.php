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
        Schema::create('ejo_team_assign', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ejo_id')
            ->constrained('ejo_tickets')
            ->cascadeOnDelete();

            $table->foreignId('team_id')
            ->constrained('teams')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejo_team_assign');
    }
};
