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
        // 1. Create table master_listrik_panels
        if (!Schema::hasTable('master_listrik_panels')) {
            Schema::create('master_listrik_panels', function (Blueprint $table) {
                $table->id();
                $table->string('nama_panel', 100)->unique();
                $table->string('deskripsi', 255)->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('urutan')->default(0);
                $table->timestamps();
            });
        }

        // 2. Change panel_type in pemakaian_listrik_eng from enum to varchar
        if (Schema::hasTable('pemakaian_listrik_eng') && Schema::hasColumn('pemakaian_listrik_eng', 'panel_type')) {
            Schema::table('pemakaian_listrik_eng', function (Blueprint $table) {
                $table->string('panel_type', 100)->change();
            });
        }

        // 3. Add is_active & deskripsi columns to air_area_utility if not exists
        if (Schema::hasTable('air_area_utility')) {
            Schema::table('air_area_utility', function (Blueprint $table) {
                if (!Schema::hasColumn('air_area_utility', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('nama_area');
                }
                if (!Schema::hasColumn('air_area_utility', 'deskripsi')) {
                    $table->string('deskripsi', 255)->nullable()->after('is_active');
                }
            });
        }

        // 4. Add is_active to chemical_areas and chemical_types if not exists
        if (Schema::hasTable('chemical_areas')) {
            Schema::table('chemical_areas', function (Blueprint $table) {
                if (!Schema::hasColumn('chemical_areas', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('nama_area');
                }
            });
        }

        if (Schema::hasTable('chemical_types')) {
            Schema::table('chemical_types', function (Blueprint $table) {
                if (!Schema::hasColumn('chemical_types', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('satuan');
                }
            });
        }

        // 5. Seed default Listrik panels
        $defaultPanels = [
            'MDP', 'SDP1', 'SDP2', 'SDP3', 'SDP4', 'SDP5', 'SDP6',
            'SDP7', 'SDP8', 'SDP9', 'SDP10', 'SDP11', 'SDP12', 'SDP13', 'SDP14'
        ];
        foreach ($defaultPanels as $idx => $p) {
            DB::table('master_listrik_panels')->updateOrInsert(
                ['nama_panel' => $p],
                ['urutan' => $idx + 1, 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // 6. Seed default Air areas if empty
        $airCount = DB::table('air_area_utility')->count();
        if ($airCount === 0) {
            $defaultAir = [
                'Sumur 1', 'Sumur 2', 'Sumur 4', 'Sumur 5', 'CT RO', 'CT WS',
                'Green Belt', 'Outlet Fresh Water 1', 'Outlet Fresh Water 2',
                'Outlet Storage RO Reject', 'Outlet Storage WS', 'PDAM'
            ];
            foreach ($defaultAir as $name) {
                DB::table('air_area_utility')->updateOrInsert(
                    ['nama_area' => $name],
                    ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // 7. Seed default Chemical areas and types if empty
        $chemAreaCount = DB::table('chemical_areas')->count();
        if ($chemAreaCount === 0) {
            // Boiler
            $boilerId = DB::table('chemical_areas')->insertGetId([
                'nama_area' => 'Boiler',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::table('chemical_types')->insert([
                ['chemical_area_id' => $boilerId, 'nama_chemical' => 'SRTF', 'satuan' => 'Liter', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['chemical_area_id' => $boilerId, 'nama_chemical' => 'SCF', 'satuan' => 'Liter', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);

            // WWTP
            $wwtpId = DB::table('chemical_areas')->insertGetId([
                'nama_area' => 'WWTP',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $wwtpChemicals = [
                ['PAC powder 1', 'Kg'],
                ['PAC powder 2', 'Kg'],
                ['BE-100', 'Kg'],
                ['C-204', 'Kg'],
                ['C-9040 step 1', 'Kg'],
                ['C-9040 step 2', 'Kg'],
                ['Denfloc 945', 'Kg'],
                ['Defoamer', 'Kg'],
                ['NaOH', 'Kg'],
                ['NPK', 'Kg']
            ];
            foreach ($wwtpChemicals as $item) {
                DB::table('chemical_types')->insert([
                    'chemical_area_id' => $wwtpId,
                    'nama_chemical' => $item[0],
                    'satuan' => $item[1],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Utility
            $utilId = DB::table('chemical_areas')->insertGetId([
                'nama_area' => 'Utility',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $utilChemicals = [
                ['Chlorin', 'Kg'],
                ['SMBS', 'Kg'],
                ['PT100', 'Kg'],
                ['B4', 'Kg'],
                ['SRF', 'Kg']
            ];
            foreach ($utilChemicals as $item) {
                DB::table('chemical_types')->insert([
                    'chemical_area_id' => $utilId,
                    'nama_chemical' => $item[0],
                    'satuan' => $item[1],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_listrik_panels');
    }
};
