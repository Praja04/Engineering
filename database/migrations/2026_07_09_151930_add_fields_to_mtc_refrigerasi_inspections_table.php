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
            $table->boolean('check_pelumasan_blower')->nullable()->after('check_fan_belt_blower');
            $table->boolean('check_suhu_supply')->nullable()->after('check_jalur_return_udara');
            $table->boolean('check_suhu_return')->nullable()->after('check_suhu_supply');
            $table->boolean('check_flow_supply')->nullable()->after('check_suhu_return');
            $table->boolean('check_flow_return')->nullable()->after('check_flow_supply');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mtc_refrigerasi_inspections', function (Blueprint $table) {
            $table->dropColumn([
                'check_pelumasan_blower',
                'check_suhu_supply',
                'check_suhu_return',
                'check_flow_supply',
                'check_flow_return',
            ]);
        });
    }
};
