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
        Schema::create('epr_predictive_maintenances', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('tech_name');
            $table->string('area');
            $table->string('wo_ref')->nullable();
            $table->text('work_description');
            $table->time('time_start');
            $table->time('time_end');
            $table->enum('status', ['open', 'progress', 'done', 'onhold'])->default('progress');
            $table->text('notes')->nullable();
            $table->boolean('is_adhoc')->default(false);
            $table->string('adhoc_title')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('epr_predictive_maintenances')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('epr_predictive_maintenance_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('predictive_maintenance_id');
            $table->string('photo_path');
            $table->timestamps();

            $table->foreign('predictive_maintenance_id', 'epr_pm_photos_pm_id_foreign')
                  ->references('id')
                  ->on('epr_predictive_maintenances')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epr_predictive_maintenance_photos');
        Schema::dropIfExists('epr_predictive_maintenances');
    }
};
