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
        Schema::table('esp_shift_reports', function (Blueprint $table) {
            $table->decimal('kondensat', 10, 2)->nullable()->after('dosis');
        });

        // Sync existing data
        $reports = Illuminate\Support\Facades\DB::table('esp_shift_reports')->get();
        foreach ($reports as $report) {
            if (
                !is_null($report->feed_tank_akhir) &&
                !is_null($report->feed_tank_awal) &&
                !is_null($report->pemakaian_air) &&
                $report->pemakaian_air != 0
            ) {

                $val = abs(100 - ((($report->feed_tank_akhir - $report->feed_tank_awal) / $report->pemakaian_air) * 100));
                Illuminate\Support\Facades\DB::table('esp_shift_reports')
                    ->where('id', $report->id)
                    ->update(['kondensat' => $val]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('esp_shift_reports', function (Blueprint $table) {
            $table->dropColumn('kondensat');
        });
    }
};
