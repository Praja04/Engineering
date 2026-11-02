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
        Schema::create('mtc_master_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('mtc_master_checklist_groups')->cascadeOnDelete();
            $table->string('jenis_mtc');
            $table->string('field_key')->nullable(); // Untuk mapping legacy column (e.g. electrical_motor)
            $table->string('label'); // Nama item checklist (e.g. Kondisi Electrical)
            $table->text('standar')->nullable(); // Standar pemeliharaan / petunjuk
            $table->string('tipe_input')->default('boolean_ok_ng'); // boolean_ok_ng, text, number
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['jenis_mtc', 'is_active']);
            $table->index('field_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_master_checklists');
    }
};
