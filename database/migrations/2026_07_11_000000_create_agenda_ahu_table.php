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
        Schema::create('agenda_ahu', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');  // 1-12
            $table->unsignedSmallInteger('tahun'); // e.g. 2026
            $table->enum('status', ['draft', 'submitted', 'approved_foreman', 'approved_supervisor', 'rejected'])->default('draft');
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('foreman_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_foreman_at')->nullable();
            $table->timestamp('approved_supervisor_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('agenda_ahu_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_ahu_id')->nullable()->constrained('agenda_ahu')->nullOnDelete();
            $table->date('tanggal')->unique(); // Daily log is unique

            // 26 Checklist fields (all nullable strings containing OK / NOK)
            $table->string('kelistrikan_ahu_1', 10)->nullable();
            $table->string('kelistrikan_ahu_2', 10)->nullable();
            $table->string('kelistrikan_ahu_3', 10)->nullable();
            $table->string('kelistrikan_ahu_4', 10)->nullable();
            
            $table->string('pressur_gauge_in_ahu_1', 10)->nullable();
            $table->string('pressur_gauge_in_ahu_2', 10)->nullable();
            $table->string('pressur_gauge_in_ahu_3', 10)->nullable();
            $table->string('pressur_gauge_in_ahu_4', 10)->nullable();
            
            $table->string('pressur_gauge_out_ahu_1', 10)->nullable();
            $table->string('pressur_gauge_out_ahu_2', 10)->nullable();
            $table->string('pressur_gauge_out_ahu_3', 10)->nullable();
            $table->string('pressur_gauge_out_ahu_4', 10)->nullable();
            
            $table->string('temp_gauge_in_ahu_1', 10)->nullable();
            $table->string('temp_gauge_in_ahu_2', 10)->nullable();
            $table->string('temp_gauge_in_ahu_3', 10)->nullable();
            $table->string('temp_gauge_in_ahu_4', 10)->nullable();
            
            $table->string('temp_gauge_out_ahu_1', 10)->nullable();
            $table->string('temp_gauge_out_ahu_2', 10)->nullable();
            $table->string('temp_gauge_out_ahu_3', 10)->nullable();
            $table->string('temp_gauge_out_ahu_4', 10)->nullable();
            
            $table->string('clean_filter_strainer_1', 10)->nullable();
            $table->string('clean_filter_strainer_2', 10)->nullable();
            $table->string('clean_filter_strainer_3', 10)->nullable();
            $table->string('clean_filter_strainer_4', 10)->nullable();
            
            $table->string('clean_filter_bebas_ahu', 10)->nullable();
            $table->string('inspeksi_h_ahu_1_4', 10)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_ahu_details');
        Schema::dropIfExists('agenda_ahu');
    }
};
