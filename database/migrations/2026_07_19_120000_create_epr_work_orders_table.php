<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('epr_work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('area');
            $table->string('machine')->nullable();
            $table->enum('priority', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->enum('status', ['open', 'assigned', 'progress', 'done', 'approved', 'rejected'])->default('open');
            $table->date('target_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('epr_wo_assignees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('duration_minutes')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('work_order_id', 'epr_wo_assign_wo_fk')
                  ->references('id')
                  ->on('epr_work_orders')
                  ->onDelete('cascade');
        });

        // Add work_order_id to existing predictive_maintenances table
        Schema::table('epr_predictive_maintenances', function (Blueprint $table) {
            $table->unsignedBigInteger('work_order_id')->nullable()->after('wo_ref');

            $table->foreign('work_order_id', 'epr_pm_wo_fk')
                  ->references('id')
                  ->on('epr_work_orders')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('epr_predictive_maintenances', function (Blueprint $table) {
            $table->dropForeign('epr_pm_wo_fk');
            $table->dropColumn('work_order_id');
        });
        Schema::dropIfExists('epr_wo_assignees');
        Schema::dropIfExists('epr_work_orders');
    }
};
