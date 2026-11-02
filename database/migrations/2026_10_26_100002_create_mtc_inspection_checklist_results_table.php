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
        Schema::create('mtc_inspection_checklist_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->cascadeOnDelete();
            $table->foreignId('checklist_id')->constrained('mtc_master_checklists')->cascadeOnDelete();
            $table->string('status')->nullable(); // OK, NG, NA
            $table->text('nilai')->nullable(); // For readings (temperature, pressure, etc.)
            $table->text('keterangan')->nullable(); // Keterangan jika NG / catatan temuan
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['mtc_main_id', 'checklist_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_inspection_checklist_results');
    }
};
