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
        Schema::table('wwtp_chemical_standards', function (Blueprint $table) {
            $table->double('standar_pemakaian')->default(0)->after('harga_standar');
        });

        // Set default standards for existing chemicals
        \Illuminate\Support\Facades\DB::table('wwtp_chemical_standards')->where('chemical_name', 'like', '%NAOH%')->update(['standar_pemakaian' => 6000]);
        \Illuminate\Support\Facades\DB::table('wwtp_chemical_standards')->where('chemical_name', 'like', '%PAC%')->update(['standar_pemakaian' => 4000]);
        \Illuminate\Support\Facades\DB::table('wwtp_chemical_standards')->where('chemical_name', 'like', '%Denfloc 945 DAF%')->update(['standar_pemakaian' => 60]);
        \Illuminate\Support\Facades\DB::table('wwtp_chemical_standards')->where('chemical_name', 'like', '%9040%')->update(['standar_pemakaian' => 50]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwtp_chemical_standards', function (Blueprint $table) {
            $table->dropColumn('standar_pemakaian');
        });
    }
};
