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
        if (!Schema::hasTable('daily_activity_logs')) {
            Schema::create('daily_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->date('log_date')->index();
                $table->string('group_type', 50)->default('TIM_EJO'); // TIM_EJO or TIM_DRAFTER
                $table->string('engineer_name', 100);
                $table->string('role', 50)->nullable();
                $table->text('activity');
                $table->string('ejo_id', 50)->nullable();
                $table->string('ejo_title', 255)->nullable();
                $table->string('created_by', 100)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_activity_logs');
    }
};
