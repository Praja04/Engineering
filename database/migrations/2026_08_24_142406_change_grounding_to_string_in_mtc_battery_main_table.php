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
            $table->boolean('kondisi_plug_battery')->nullable()->change();
            $table->string('grounding', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mtc_battery_main', function (Blueprint $table) {
            $table->string('kondisi_plug_battery')->nullable()->change();
            $table->decimal('grounding', 10, 2)->nullable()->change();
        });
    }
};
