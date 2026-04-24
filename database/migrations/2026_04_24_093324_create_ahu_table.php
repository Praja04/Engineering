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
        Schema::create('ahu', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');  // 1-12
            $table->unsignedSmallInteger('tahun'); // e.g. 2025
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

        Schema::create(
            'ahu_details',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('ahu_id')->nullable()->constrained('ahu')->nullOnDelete();
                $table->date('tanggal');
                $table->time('jam');

                // AHU 1
                $table->decimal('ampere_1', 10, 2)->nullable();
                $table->decimal('set_temp_1', 10, 2)->nullable();
                $table->decimal('pressure_in_1', 10, 2)->nullable();
                $table->decimal('pressure_out_1', 10, 2)->nullable();
                $table->decimal('ct_in_1', 10, 2)->nullable();
                $table->decimal('ct_out_1', 10, 2)->nullable();

                // AHU 2
                $table->decimal('ampere_2', 10, 2)->nullable();
                $table->decimal('set_temp_2', 10, 2)->nullable();
                $table->decimal('pressure_in_2', 10, 2)->nullable();
                $table->decimal('pressure_out_2', 10, 2)->nullable();
                $table->decimal('ct_in_2', 10, 2)->nullable();
                $table->decimal('ct_out_2', 10, 2)->nullable();

                // AHU 3
                $table->decimal('ampere_3', 10, 2)->nullable();
                $table->decimal('set_temp_3', 10, 2)->nullable();
                $table->decimal('pressure_in_3', 10, 2)->nullable();
                $table->decimal('pressure_out_3', 10, 2)->nullable();
                $table->decimal('ct_in_3', 10, 2)->nullable();
                $table->decimal('ct_out_3', 10, 2)->nullable();

                // AHU 4
                $table->decimal('ampere_4', 10, 2)->nullable();
                $table->decimal('set_temp_4', 10, 2)->nullable();
                $table->decimal('pressure_in_4', 10, 2)->nullable();
                $table->decimal('pressure_out_4', 10, 2)->nullable();
                $table->decimal('ct_in_4', 10, 2)->nullable();
                $table->decimal('ct_out_4', 10, 2)->nullable();

                $table->decimal('temp_out_1', 10, 2)->nullable();
                $table->decimal('temp_out_2', 10, 2)->nullable();
                $table->decimal('temp_out_3', 10, 2)->nullable();
                $table->decimal('temp_out_4', 10, 2)->nullable();

                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahu_details');
        Schema::dropIfExists('ahu');
    }
};
