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
        Schema::create('ejo_tickets', function (Blueprint $table) {
            $table->id();

            $table->string('ticket_id')->unique();
            $table->string('os_in')->nullable();
            $table->string('department')->nullable();

            $table->datetime('request_date')->nullable();

            $table->string('category')->nullable();
            $table->string('module')->nullable();

            $table->string('subject')->nullable();
            $table->text('description')->nullable();

            $table->string('requestor')->nullable();

            $table->string('status')->default('open');

            $table->string('type')->nullable();

            $table->date('schedule')->nullable();
            $table->integer('est_time')->nullable();

            $table->date('date_done')->nullable();

            $table->foreignId('classification_id')
            ->nullable()
                ->constrained('ejo_classifications')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejo_tickets');
    }
};
