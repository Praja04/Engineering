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
        Schema::create('pemantauan_pompa_utility', function (Blueprint $table) {
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

        Schema::create('pemantauan_pompa_utility_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemantauan_pompa_utility_id')->nullable()->constrained('pemantauan_pompa_utility', 'id', 'fk_pemantauan_pompa_details_main')->nullOnDelete();
            $table->date('tanggal')->unique(); // Daily log is unique

            // 22 Checklist fields (all nullable strings containing OK / NOK)
            $table->string('ampere_pompa_10p3', 10)->nullable();
            $table->string('ampere_pompa_10p3a', 10)->nullable();
            $table->string('ampere_pompa_10p4', 10)->nullable();
            $table->string('ampere_pompa_10p4a', 10)->nullable();
            $table->string('ampere_pompa_10p5b', 10)->nullable();
            $table->string('ampere_pompa_20p1', 10)->nullable();
            $table->string('ampere_pompa_20p1a', 10)->nullable();
            $table->string('ampere_pompa_20p2', 10)->nullable();
            $table->string('ampere_pompa_20p2a', 10)->nullable();
            $table->string('ampere_pompa_60p1', 10)->nullable();
            $table->string('ampere_pompa_60p2', 10)->nullable();
            $table->string('ampere_pompa_60p3', 10)->nullable();
            $table->string('ampere_pompa_hp_pump', 10)->nullable();
            $table->string('ampere_pompa_cip_pump', 10)->nullable();
            $table->string('ampere_pompa_tf_ws', 10)->nullable();
            
            $table->string('ampere_fan_1', 10)->nullable();
            $table->string('ampere_fan_2', 10)->nullable();
            $table->string('ampere_fan_3', 10)->nullable();
            $table->string('ampere_fan_4', 10)->nullable();
            
            $table->string('ampere_pompa_ct_10000p1', 10)->nullable();
            $table->string('ampere_pompa_ct_10000p2', 10)->nullable();
            $table->string('ampere_pompa_ct_10000p3', 10)->nullable();

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
        Schema::dropIfExists('pemantauan_pompa_utility_details');
        Schema::dropIfExists('pemantauan_pompa_utility');
    }
};
