<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wwtp_effluent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wwtp_record_id')->constrained('wwtp_records')->onDelete('cascade');

            $table->decimal('full_proses', 10, 2)->default(0);
            $table->decimal('daf_pre', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wwtp_effluent');
    }
};
