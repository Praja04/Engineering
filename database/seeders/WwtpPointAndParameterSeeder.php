<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WwtpPointAndParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Seed wwtp_point
        $points = [
            'Sparta',
            'Influent COD',
            'Inlet Anaerob',
            'New Anaerob',
            'Outlet Anaerob',
            'Aerasi-1',
            'Aerasi-2',
            'Aerasi-3',
            'Aerasi-4',
            'Aerasi-5',
            'Aerasi-6',
            'Lumpur Aktif',
            'Clarifier 1',
            'Clarifier 2',
            'Outlet DAF',
            'Filtrat SCP',
            'Outlet Sand Filter',
            'Pit Garam',
            'Effluent COD (max 300 ppm)',
            'SDM 1'
        ];

        foreach ($points as $point) {
            DB::table('wwtp_point')->updateOrInsert(
                ['point_name' => $point],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        // 2. Seed wwtp_parameters
        $parameters = [
            [
                'parameter_name' => 'COD (Chemical Oxygen Demand)',
                'unit' => 'mg/L'
            ],
            [
                'parameter_name' => 'TSS (Total Suspended Solids)',
                'unit' => 'mg/L'
            ],
            [
                'parameter_name' => 'pH (Power of Hydrogen)',
                'unit' => 'pH'
            ],
            [
                'parameter_name' => 'EC (Electrical Conductivity)',
                'unit' => '%'
            ],
            [
                'parameter_name' => 'DO (Dissolved Oxygen)',
                'unit' => 'mg/L'
            ]
        ];

        foreach ($parameters as $param) {
            DB::table('wwtp_parameters')->updateOrInsert(
                ['parameter_name' => $param['parameter_name']],
                [
                    'unit' => $param['unit'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            );
        }

        // 3. Seed wwtp_standards (set as null for now, can be updated via UI later)
        $allPoints = DB::table('wwtp_point')->get();
        $allParams = DB::table('wwtp_parameters')->get();

        foreach ($allPoints as $pt) {
            foreach ($allParams as $pr) {
                DB::table('wwtp_standards')->updateOrInsert(
                    [
                        'point_id' => $pt->id,
                        'parameter_id' => $pr->id
                    ],
                    [
                        'standard_value' => null,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]
                );
            }
        }
    }
}
