<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username'   => 'admin',
                'password'   => Hash::make('admin123'),
                'jabatan'    => 'dept_head',
                'image'      => null,
                'email'      => 'admin123@gmail.com',
                'departemen' => null,
                'bagian'     => null,
                'nik'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username'   => 'dept_head',
                'password'   => Hash::make('12345678'),
                'jabatan'    => 'dept_head',
                'image'      => null,
                'email'      => 'depthead1@example.com',
                'departemen' => 'Engineering',
                'bagian'     => 'Perakitan',
                'nik'        => 10001,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username'   => 'foreman',
                'password'   => Hash::make('12345678'),
                'jabatan'    => 'foreman',
                'image'      => null,
                'email'      => 'foreman1@example.com',
                'departemen' => 'Engineering',
                'bagian'     => 'Pengemasan',
                'nik'        => 10002,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username'   => 'supervisor',
                'password'   => Hash::make('12345678'),
                'jabatan'    => 'supervisor',
                'image'      => null,
                'email'      => 'supervisor1@example.com',
                'departemen' => 'QC',
                'bagian'     => 'Engineering',
                'nik'        => 10003,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username'   => 'operator',
                'password'   => Hash::make('12345678'),
                'jabatan'    => 'operator',
                'image'      => null,
                'email'      => 'operator1@example.com',
                'departemen' => 'Engineering',
                'bagian'     => 'Sortir',
                'nik'        => 10004,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
