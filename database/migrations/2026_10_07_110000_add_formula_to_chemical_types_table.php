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
        if (Schema::hasTable('chemical_types')) {
            Schema::table('chemical_types', function (Blueprint $table) {
                if (!Schema::hasColumn('chemical_types', 'tipe_perhitungan')) {
                    $table->string('tipe_perhitungan', 20)->default('langsung')->after('satuan');
                }
                if (!Schema::hasColumn('chemical_types', 'rumus_formula')) {
                    $table->text('rumus_formula')->nullable()->after('tipe_perhitungan');
                }
            });
        }

        // Seed initial formulas for WWTP chemicals
        $wwtpFormulas = [
            'PAC powder 1'   => '{rh} * ({nilai} * 60 * 7.6 / 100) / 1000',
            'PAC powder 2'   => '{rh} * ({nilai} * 60 * 12.5 / 100) / 1000',
            'BE-100'         => '{rh} * ({nilai} * 60 * 2.5 / 100) / 1000',
            'C-204'          => '{rh} * ({nilai} * 60 * 1 / 100) / 1000',
            'C-9040 step 1'  => '{rh} * ({nilai} * 60 * 0.11 / 100) / 1000',
            'C-9040 step 2'  => '{rh} * ({nilai} * 60 * 0.35 / 100) / 1000',
            'Denfloc 260 PA' => '({rh} * ({nilai} / 1000 * 60) * 480) / 1000 / 1000 / 1000',
            'NaOH'           => '{rh} * ({nilai} / 1000 * 60) * 1.5',
        ];

        foreach ($wwtpFormulas as $chemName => $formula) {
            DB::table('chemical_types')
                ->whereRaw('TRIM(LOWER(nama_chemical)) = ?', [strtolower(trim($chemName))])
                ->update([
                    'tipe_perhitungan' => 'rumus',
                    'rumus_formula'    => $formula,
                    'updated_at'       => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('chemical_types')) {
            Schema::table('chemical_types', function (Blueprint $table) {
                if (Schema::hasColumn('chemical_types', 'tipe_perhitungan')) {
                    $table->dropColumn('tipe_perhitungan');
                }
                if (Schema::hasColumn('chemical_types', 'rumus_formula')) {
                    $table->dropColumn('rumus_formula');
                }
            });
        }
    }
};
