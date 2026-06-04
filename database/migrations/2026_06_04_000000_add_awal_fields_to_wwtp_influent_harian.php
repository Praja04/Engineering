<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wwtp_influent_harian', function (Blueprint $table) {
            $table->decimal('pit_sparta_awal', 10, 2)->default(0)->after('pit_sparta');
            $table->decimal('pit_garam_awal', 10, 2)->default(0)->after('pit_garam');
            $table->decimal('pit_domestik_awal', 10, 2)->default(0)->after('pit_domestik');
            $table->decimal('pit_produksi_step3_awal', 15, 2)->default(0)->after('pit_produksi_step3');
            $table->decimal('pit_storage_awal', 15, 2)->default(0)->after('pit_storage');
            $table->decimal('pit_proses_wwtp2_awal', 15, 2)->default(0)->after('pit_proses_wwtp2');
            $table->decimal('pit_outlet_awal', 15, 2)->default(0)->after('pit_outlet');
            $table->decimal('pit_boiler_awal', 15, 2)->default(0)->after('pit_boiler');
        });

        // Recalculate _awal fields for all existing records
        $records = DB::table('wwtp_influent_harian')
            ->orderBy('tanggal', 'asc')
            ->orderByRaw("CASE 
                WHEN shift = 'shift1' THEN 1 
                WHEN shift = 'shift2' THEN 2 
                WHEN shift = 'shift3' THEN 3 
                ELSE 4 
            END ASC")
            ->get();

        $prev = null;
        foreach ($records as $record) {
            $updateData = [];
            $fields = [
                'pit_sparta', 'pit_garam', 'pit_domestik', 'pit_produksi_step3',
                'pit_storage', 'pit_proses_wwtp2', 'pit_outlet', 'pit_boiler'
            ];
            foreach ($fields as $field) {
                $awalField = $field . '_awal';
                $updateData[$awalField] = $prev ? $prev->$field : 0;
            }
            DB::table('wwtp_influent_harian')
                ->where('id', $record->id)
                ->update($updateData);
            
            $prev = $record;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwtp_influent_harian', function (Blueprint $table) {
            $table->dropColumn([
                'pit_sparta_awal',
                'pit_garam_awal',
                'pit_domestik_awal',
                'pit_produksi_step3_awal',
                'pit_storage_awal',
                'pit_proses_wwtp2_awal',
                'pit_outlet_awal',
                'pit_boiler_awal',
            ]);
        });
    }
};
