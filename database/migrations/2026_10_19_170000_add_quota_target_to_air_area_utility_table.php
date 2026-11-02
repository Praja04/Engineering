<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('air_area_utility')) {
            Schema::table('air_area_utility', function (Blueprint $table) {
                if (!Schema::hasColumn('air_area_utility', 'quota_target')) {
                    $table->decimal('quota_target', 10, 2)->nullable()->after('deskripsi');
                }
            });

            // Seed default quotas for existing raw water sources if null
            $defaultQuotas = [
                'Sumur 1' => 59.00,
                'Sumur 2' => 73.00,
                'Sumur 4' => 28.00,
                'Sumur 5' => 32.00,
                'PDAM'    => 14.00,
            ];

            foreach ($defaultQuotas as $nama => $quota) {
                DB::table('air_area_utility')
                    ->where('nama_area', $nama)
                    ->whereNull('quota_target')
                    ->update(['quota_target' => $quota]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('air_area_utility')) {
            Schema::table('air_area_utility', function (Blueprint $table) {
                if (Schema::hasColumn('air_area_utility', 'quota_target')) {
                    $table->dropColumn('quota_target');
                }
            });
        }
    }
};
