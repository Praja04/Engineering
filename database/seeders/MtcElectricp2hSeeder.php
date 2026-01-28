<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MtcElectricp2hSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            [
                'item_pengecekan' => 'Check level minyak rem',
                'kondisi_normal' => 'Berada di level max',
                'urutan' => 1,
            ],
            [
                'item_pengecekan' => 'Check level oli hydraulic',
                'kondisi_normal' => 'Berada di level max',
                'urutan' => 2,
            ],
            [
                'item_pengecekan' => 'Check isi air accu',
                'kondisi_normal' => 'Berada di level standar',
                'urutan' => 3,
            ],
            [
                'item_pengecekan' => 'Check baterai',
                'kondisi_normal' => 'Tidak kurang dari 30%',
                'urutan' => 4,
            ],
            [
                'item_pengecekan' => 'Hydraulic system',
                'kondisi_normal' => 'Berfungsi dengan baik dan terlumasi',
                'urutan' => 5,
            ],
            [
                'item_pengecekan' => 'Selang hydraulic',
                'kondisi_normal' => 'Tidak ada kebocoran oli',
                'urutan' => 6,
            ],
            [
                'item_pengecekan' => 'Lift chains',
                'kondisi_normal' => 'Kekencangan kanan dan kiri sama serta terlumasi',
                'urutan' => 7,
            ],
            [
                'item_pengecekan' => 'Pengecekan fork',
                'kondisi_normal' => 'Tidak bengkok dan tidak patah',
                'urutan' => 8,
            ],
            [
                'item_pengecekan' => 'Check body unit',
                'kondisi_normal' => 'Tidak lecet dan tidak penyok',
                'urutan' => 9,
            ],
            [
                'item_pengecekan' => 'Check kombinasi lampu kiri',
                'kondisi_normal' => 'Menyala normal dan tidak pecah',
                'urutan' => 10,
            ],
            [
                'item_pengecekan' => 'Check kombinasi lampu kanan',
                'kondisi_normal' => 'Menyala normal dan tidak pecah',
                'urutan' => 11,
            ],
            [
                'item_pengecekan' => 'Check lampu sorot / head lamp',
                'kondisi_normal' => 'Menyala normal dan tidak pecah',
                'urutan' => 12,
            ],
            [
                'item_pengecekan' => 'Check lampu sign depan kanan',
                'kondisi_normal' => 'Menyala normal dan tidak pecah',
                'urutan' => 13,
            ],
            [
                'item_pengecekan' => 'Check lampu sign depan kiri',
                'kondisi_normal' => 'Menyala normal dan tidak pecah',
                'urutan' => 14,
            ],
            [
                'item_pengecekan' => 'Check klakson / horn',
                'kondisi_normal' => 'Berbunyi saat tombol ditekan',
                'urutan' => 15,
            ],
            [
                'item_pengecekan' => 'Check buzzer back',
                'kondisi_normal' => 'Berbunyi normal saat maju dan mundur',
                'urutan' => 16,
            ],
            [
                'item_pengecekan' => 'Check kaca spion',
                'kondisi_normal' => 'Terpasang dan tidak pecah',
                'urutan' => 17,
            ],
            [
                'item_pengecekan' => 'Check kekencangan baut roda',
                'kondisi_normal' => 'Kencang dan tidak patah',
                'urutan' => 18,
            ],
            [
                'item_pengecekan' => 'Check ban',
                'kondisi_normal' => 'Masih bagus dan layak pakai',
                'urutan' => 19,
            ],
            [
                'item_pengecekan' => 'Check kebersihan unit',
                'kondisi_normal' => 'Bersih dari kotoran dan debu',
                'urutan' => 20,
            ],
            [
                'item_pengecekan' => 'Check panel display',
                'kondisi_normal' => 'Berfungsi normal, tidak pecah, dan tidak ada alarm',
                'urutan' => 21,
            ],
            [
                'item_pengecekan' => 'Hours meter (jam operasional)',
                'kondisi_normal' => 'Dicatat sesuai aktual di unit',
                'urutan' => 22,
            ],
            [
                'item_pengecekan' => 'Sistem kemudi',
                'kondisi_normal' => 'Tidak berat dan berjalan lancar',
                'urutan' => 23,
            ],
        ];

        foreach ($items as $item) {
            DB::table('mtc_electric_p2h_items')->updateOrInsert(
                [
                    'item_pengecekan' => $item['item_pengecekan'],
                    'kondisi_normal' => $item['kondisi_normal'],
                    'aktif' => true,
                    'urutan' => $item['urutan'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
