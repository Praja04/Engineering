<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MtcSipilItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            [
                'jenis_perawatan' => 'Plumbing',
                'standar_pemeliharaan' => 'Tidak ada kebocoran dan mampet saluran air pada pipa',
                'urutan' => 1,
            ],
            [
                'jenis_perawatan' => 'Plafon',
                'standar_pemeliharaan' => 'Tidak berlubang, berjamur dan retakan pada palfon',
                'urutan' => 2,
            ],
            [
                'jenis_perawatan' => 'Lantai',
                'standar_pemeliharaan' => 'Tidak berlubang, retak, gompal dan jamur pada lantai',
                'urutan' => 3,
            ],
            [
                'jenis_perawatan' => 'Dinding',
                'standar_pemeliharaan' => 'Tidak ada dinding retak, gompal dan cat atau wallpaper (mengelupas, berjamur, kusam)',
                'urutan' => 4,
            ],
            [
                'jenis_perawatan' => 'Jendela',
                'standar_pemeliharaan' => 'Engsel berfungsi dengan baik, tidak ada retakan kaca atau cover, cat tidak kusam dan tulangan tidak cacat',
                'urutan' => 5,
            ],
            [
                'jenis_perawatan' => 'Pintu',
                'standar_pemeliharaan' => 'Engsel berfungsi dengan baik, tidak ada retakan kaca atau cover, cat tidak kusam dan tulangan tidak cacat',
                'urutan' => 6,
            ],
            [
                'jenis_perawatan' => 'Rooling / Fast Door',
                'standar_pemeliharaan' => 'Suara halus, rel terlubrikasi, naik dan turun normal',
                'urutan' => 7,
            ],
        ];

        foreach ($items as $item) {
            DB::table('mtc_sipil_items')->updateOrInsert(
                [
                    'jenis_perawatan' => $item['jenis_perawatan'],
                ],
                [
                    'standar_pemeliharaan' => $item['standar_pemeliharaan'],
                    'aktif' => true,
                    'urutan' => $item['urutan'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
