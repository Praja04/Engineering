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
        Schema::table('mtc_battery_main', function (Blueprint $table) {
            $table->decimal('grounding', 10, 2)->nullable()->after('total_voltase');
        });

        Schema::table('mtc_battery_inspections', function (Blueprint $table) {
            $table->dropColumn('grounding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mtc_battery_inspections', function (Blueprint $table) {
            $table->decimal('grounding', 10, 2)->nullable();
        });

        Schema::table('mtc_battery_main', function (Blueprint $table) {
            $table->dropColumn('grounding');
        });
    }
};
