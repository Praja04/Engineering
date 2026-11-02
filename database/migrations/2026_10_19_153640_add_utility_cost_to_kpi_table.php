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
        Schema::table('kpi', function (Blueprint $table) {
            $table->decimal('utility_cost', 15, 2)->nullable()->after('batubara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi', function (Blueprint $table) {
            $table->dropColumn('utility_cost');
        });
    }
};
