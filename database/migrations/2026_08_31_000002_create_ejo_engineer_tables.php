<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. general_ejos
        if (! Schema::hasTable('general_ejos')) {
            Schema::create('general_ejos', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('title');
                $table->string('dept')->nullable();
                $table->string('category')->nullable();
                $table->string('priority')->nullable();
                $table->string('location')->nullable();
                $table->string('targetDate')->nullable();
                $table->string('estDate')->nullable();
                $table->string('status')->nullable();
                $table->string('engineer')->nullable();
                $table->bigInteger('estCost')->nullable()->default(0);
                $table->bigInteger('actCost')->nullable()->default(0);
                $table->text('description')->nullable();
                $table->text('logs')->nullable();
                $table->string('requester')->nullable();
                $table->boolean('is_archived')->default(false);
                $table->text('approvals')->nullable();
                $table->string('createdDate')->nullable();
                $table->integer('quantity')->default(1);
                $table->integer('qty_needed')->default(0);
                $table->integer('qty_stock')->default(0);
                $table->string('usage_type')->default('Kebutuhan Mesin');
                $table->string('purpose')->default('Kebutuhan Mesin');
                $table->integer('qty_stock_target')->default(0);
                $table->string('mid')->nullable();
                $table->double('part_price_new')->default(0.0);
                $table->integer('repair_duration')->default(0);
                $table->double('repair_cost_per_day')->default(0.0);
                $table->text('photo_before')->nullable();
                $table->integer('qty_needed_target')->default(0);
                $table->integer('qty_needed_actual')->default(0);
                $table->integer('qty_work_confirmed')->default(0);
                $table->string('qty_work_confirmed_date')->nullable()->default('');
                $table->string('qty_work_done_date')->nullable()->default('');
            });
        }

        // 2. projects
        if (! Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('title');
                $table->string('dept')->nullable();
                $table->bigInteger('budget')->default(0);
                $table->string('targetDate')->nullable();
                $table->string('pic')->nullable();
                $table->text('desc')->nullable();
                $table->integer('phase')->default(1);
                $table->text('approvals')->nullable();
                $table->text('docs')->nullable();
                $table->integer('pr_percent')->default(0);
                $table->integer('po_percent')->default(0);
                $table->integer('gr_percent')->default(0);
                $table->string('no_io')->nullable();
                $table->string('no_moc')->nullable();
                $table->text('execution_docs')->nullable();
                $table->string('custom_status')->nullable();
                $table->text('handover_docs')->nullable();
                $table->text('handover_approvals')->nullable();
                $table->string('drawing_id')->nullable();
                $table->string('drawing_file')->nullable();
                $table->boolean('is_review_only')->default(false);
                $table->integer('pr_total_items')->default(0);
                $table->integer('pr_ready_stock')->default(0);
                $table->integer('pr_all_material')->default(0);
                $table->text('timeline')->nullable();
            });
        }

        // 3. drawings
        if (! Schema::hasTable('drawings')) {
            Schema::create('drawings', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('ejo_id')->nullable();
                $table->string('title');
                $table->string('file_path')->nullable();
                $table->string('uploader')->nullable();
                $table->string('uploaded_at')->nullable();
                $table->string('status')->nullable();
                $table->text('approvals')->nullable();
                $table->text('logs')->nullable();
                $table->string('dept')->nullable();
                $table->string('category')->nullable();
                $table->string('priority')->nullable();
                $table->string('location')->nullable();
                $table->string('targetDate')->nullable();
                $table->text('description')->nullable();
                $table->string('requester')->nullable();
                $table->string('engineer')->nullable();
                $table->string('estDate')->nullable();
                $table->string('drawing_type')->nullable();
                $table->string('sub_status')->nullable();
                $table->string('etiket_category')->nullable();
                $table->string('etiket_orientation')->nullable();
                $table->boolean('is_archived')->default(false);
            });
        }

        // 4. repair_parts
        if (! Schema::hasTable('repair_parts')) {
            Schema::create('repair_parts', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->string('code')->nullable();
                $table->integer('stock')->default(0);
                $table->string('location')->nullable();
                $table->string('ejo_id')->nullable();
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->double('price')->default(0.0);
                $table->double('cost_saving')->default(0.0);
                $table->double('original_price')->default(0.0);
                $table->string('uploader')->nullable();
            });
        }

        // 5. settings
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->text('value')->nullable();
            });
        }

        // 6. wsp_materials
        if (! Schema::hasTable('wsp_materials')) {
            Schema::create('wsp_materials', function (Blueprint $table) {
                $table->string('material')->primary();
                $table->text('description')->nullable();
                $table->double('price')->default(0.0);
            });
        }

        // 7. notifications
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('target_username')->nullable();
                $table->string('ejo_id')->nullable();
                $table->text('message')->nullable();
                $table->string('timestamp')->nullable();
                $table->boolean('is_read')->default(false);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('general_ejos');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('drawings');
        Schema::dropIfExists('repair_parts');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('wsp_materials');
        Schema::dropIfExists('notifications');
    }
};
