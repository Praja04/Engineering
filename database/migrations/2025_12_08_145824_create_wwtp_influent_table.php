<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wwtp_influent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wwtp_record_id')->constrained('wwtp_records')->onDelete('cascade');

            $table->decimal('pit_sparta', 10, 2)->default(0);
            $table->decimal('pit_garam', 10, 2)->default(0);
            $table->decimal('pit_domestik', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wwtp_influent');
    }
};
