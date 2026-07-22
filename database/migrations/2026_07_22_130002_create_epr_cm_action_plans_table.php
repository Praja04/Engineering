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
        Schema::create('epr_cm_action_plans', function (Blueprint $table) {
            $table->id();
            $table->string('month'); // YYYY-MM
            $table->string('mesin');
            $table->text('isu_utama');
            $table->text('akar_masalah')->nullable();
            $table->text('saran_perbaikan')->nullable();
            $table->string('pic')->nullable();
            $table->date('target_date')->nullable();
            $table->string('w1_status')->default('none'); // none, red, orange, yellow, green
            $table->string('w2_status')->default('none');
            $table->string('w3_status')->default('none');
            $table->string('w4_status')->default('none');
            $table->string('status')->default('Open'); // Open, Progress, Closed
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epr_cm_action_plans');
    }
};
