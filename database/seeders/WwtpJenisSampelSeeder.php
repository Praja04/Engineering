<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WwtpJenisSampelSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            // Aerasi 1 - 6
            ['nama_sampel' => 'Aerasi 1'],
            ['nama_sampel' => 'Aerasi 2'],
            ['nama_sampel' => 'Aerasi 3'],
            ['nama_sampel' => 'Aerasi 4'],
            ['nama_sampel' => 'Aerasi 5'],
            ['nama_sampel' => 'Aerasi 6'],

            // Lumpur Aktif
            ['nama_sampel' => 'Lumpur Aktif'],
            ['nama_sampel' => 'Was Aerasi'],
            ['nama_sampel' => 'Ras Aerasi'],
            ['nama_sampel' => 'Filtrat Clarifier Aerasi'],
            ['nama_sampel' => 'Was Lumpur Aktif'],
            ['nama_sampel' => 'Ras Lumpur Aktif'],
            ['nama_sampel' => 'Filtrat Clarifier Lumpur Aktif'],

            // Anaerob
            ['nama_sampel' => 'Was Anaerob'],
            ['nama_sampel' => 'Ras Anaerob'],
            ['nama_sampel' => 'Filtrat Anaerob'],
        ];

        foreach ($data as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        DB::table('wwtp_jenis_sampel')->insert($data);
    }
}
