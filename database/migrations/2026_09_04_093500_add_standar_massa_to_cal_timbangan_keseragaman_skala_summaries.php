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
        Schema::table('cal_timbangan_keseragaman_skala_summaries', function (Blueprint $table) {
            $table->decimal('standar_massa', 15, 8)->nullable()->after('beban');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cal_timbangan_keseragaman_skala_summaries', function (Blueprint $table) {
            $table->dropColumn('standar_massa');
        });
    }
};
