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
        Schema::create('eng_utility_projects', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_project', 50)->nullable()->index();
            $table->string('nama_project');
            $table->text('target_result')->nullable();
            $table->string('area', 50)->index(); // Boiler, Utility, WWTP, MTC
            $table->string('kategori', 100)->index(); // Maintenance, KPI BB dan Steam, Baku Mutu, Compliance, 5R, KPI Steam, Improvement, Cost Saving, KPI Air, Digitalisasi, KPI Korektif, dll
            $table->date('start_date')->nullable();
            $table->string('start_week', 50)->nullable(); // e.g. W1 Okt 26, W3 Jun 26
            $table->date('finish_date')->nullable();
            $table->string('finish_week', 50)->nullable(); // e.g. W1 Jan 27, W3 Agus 26
            $table->unsignedTinyInteger('progress')->default(0); // 0 - 100%
            $table->enum('status', ['Selesai', 'On Progress', 'Terlambat'])->default('On Progress')->index();
            $table->text('latest_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('eng_utility_project_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('eng_utility_projects')->onDelete('cascade');
            $table->unsignedTinyInteger('progress_sebelumnya')->default(0);
            $table->unsignedTinyInteger('progress_baru')->default(0);
            $table->text('catatan')->nullable();
            $table->date('tanggal')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eng_utility_project_logs');
        Schema::dropIfExists('eng_utility_projects');
    }
};
