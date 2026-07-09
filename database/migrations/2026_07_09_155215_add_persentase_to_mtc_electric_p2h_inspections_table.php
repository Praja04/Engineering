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
        Schema::table('mtc_electric_p2h_inspections', function (Blueprint $table) {
            $table->decimal('persentase', 5, 2)->nullable()->after('hours_meter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mtc_electric_p2h_inspections', function (Blueprint $table) {
            $table->dropColumn('persentase');
        });
    }
};
