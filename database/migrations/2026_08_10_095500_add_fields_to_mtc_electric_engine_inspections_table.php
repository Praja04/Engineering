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
        Schema::table('mtc_electric_engine_inspections', function (Blueprint $table) {
            $table->boolean('check_boot_steering')->nullable()->after('check_air_spring');
            $table->boolean('check_wheel_chain')->nullable()->after('check_boot_steering');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mtc_electric_engine_inspections', function (Blueprint $table) {
            $table->dropColumn(['check_boot_steering', 'check_wheel_chain']);
        });
    }
};
