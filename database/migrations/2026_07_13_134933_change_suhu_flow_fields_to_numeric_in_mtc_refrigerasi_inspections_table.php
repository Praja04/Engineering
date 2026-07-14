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
        Schema::table('mtc_refrigerasi_inspections', function (Blueprint $table) {
            $table->decimal('check_suhu_supply', 8, 2)->nullable()->change();
            $table->decimal('check_suhu_return', 8, 2)->nullable()->change();
            $table->decimal('check_flow_supply', 8, 2)->nullable()->change();
            $table->decimal('check_flow_return', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mtc_refrigerasi_inspections', function (Blueprint $table) {
            $table->boolean('check_suhu_supply')->nullable()->change();
            $table->boolean('check_suhu_return')->nullable()->change();
            $table->boolean('check_flow_supply')->nullable()->change();
            $table->boolean('check_flow_return')->nullable()->change();
        });
    }
};
