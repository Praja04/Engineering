<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MtcDieselp2hSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            ['Check Klakson', 'Bunyi ketika tombol ditekan'],
            ['Check Buzzer back', 'Berbunyi normal saat maju / mundur'],
            ['Check Kondisi & level oli Mesin', 'Berada di level max, tidak ada kebocoran'],
            ['Check Kondisi level radiator & Hose', 'Berada di level max, tidak ada kebocoran'],
            ['Check Water pump', 'Tidak ada kebocoran'],
            ['Check Injection pump, injector, Piping', 'Tidak ada kebocoran'],
            ['Check Fan & V-belt', 'Berfungsi dengan baik dan V-belt tidak retak/putus'],
            ['Check Turbocharger & manifold', 'Berfungsi dengan baik dan terlubrikasi'],
            ['Check Automatic Tensioner belt', 'Berfungsi dengan baik'],
            ['Check Fungsi starting motor', 'Berfungsi dengan baik'],
            ['Check Fungsi alternator', 'Berfungsi dengan baik'],
            ['Check Control display', 'Berfungsi normal, tidak pecah dan tidak ada alarm'],
            ['Check Kondisi & level oli transmisi', 'Berada di level max, tidak ada kebocoran'],
            ['Check Kondisi aki & level air aki', 'Berada di level max, accu tidak drop, serta bersih'],
            ['Check Engine mounting', 'Berfungsi dengan baik'],
            ['Check Filter oli transmisi', 'Tidak ada kebocoran oli'],
            ['Check Fungsi rem', 'Berfungsi dengan baik dan tidak blong'],
            ['Check Fungsi kopling', 'Berfungsi dengan baik dan tidak macet'],
            ['Check Kondisi & level hydraulic oil', 'Berada di level max, tidak ada kebocoran'],
            ['Check Fungsi hydraulic system', 'Berfungsi dengan baik dan terlubrikasi'],
            ['Check Fungsi steering system', 'Tidak berat dan lancar'],
            ['Check Kondisi back rest & body', 'Tidak ada cacat / penyok'],
            ['Check Kaca sepion', 'Berfungsi dengan baik dan lengkap'],
            ['Check Kondisi bucket & pin bucket', 'Berfungsi dengan baik, tidak ada retak / hilang'],
            ['Check Kondisi dump, pin & bushing', 'Berfungsi dan tidak retak / hilang'],
            ['Check Kondisi seal hydraulic', 'Tidak ada kebocoran oli'],
            ['Check Kondisi roda/ban & baut', 'Masih bagus dan layak pakai, baut utuh terpasang kencang'],
            ['Check Lampu depan & belakang kanan kiri', 'Menyala normal dan tidak pecah'],
            ['Check Baut bearing molen dan gandengan', 'Baut terpasang utuh dan kencang'],
            ['Check Baut hanger as roda', 'Baut terpasang utuh dan kencang'],
            ['Hours meter (jam operasional)', 'Catat hours meter sesuai aktual di unit'],
            ['Cek kondisi baut grease', 'Baut aus dan ter-greasing'],
            ['Cek kondisi katup pembuangan angin', 'Berfungsi dengan baik'],
        ];

        $data = [];
        foreach ($items as $i => $item) {
            $data[] = [
                'item_pengecekan' => $item[0],
                'kondisi_normal'  => $item[1],
                'urutan'          => $i + 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        DB::table('mtc_diesel_p2h_items')->insert($data);
    }
}
