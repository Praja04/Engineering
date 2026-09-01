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
        if (! Schema::hasTable('ejo_engineer_notifications')) {
            Schema::create('ejo_engineer_notifications', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('target_username')->nullable();
                $table->string('ejo_id')->nullable();
                $table->text('message')->nullable();
                $table->string('timestamp')->nullable();
                $table->boolean('is_read')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejo_engineer_notifications');
    }
};
