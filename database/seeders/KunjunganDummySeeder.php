<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KunjunganDummySeeder extends Seeder
{
    public function run(): void
    {
        $kunjungan = [
            [
                'id_kunjungan' => 1,
                'start_kunjungan' => '2026-01-10 08:05:00',
                'end_kunjungan' => '2026-01-10 08:45:00',
                'id_siswa_tetap' => 3,
            ],
            [
                'id_kunjungan' => 2,
                'start_kunjungan' => '2026-01-12 09:10:00',
                'end_kunjungan' => '2026-01-12 09:55:00',
                'id_siswa_tetap' => 5,
            ],
            [
                'id_kunjungan' => 3,
                'start_kunjungan' => '2026-01-18 10:00:00',
                'end_kunjungan' => '2026-01-18 10:35:00',
                'id_siswa_tetap' => 6,
            ],
            [
                'id_kunjungan' => 4,
                'start_kunjungan' => '2026-02-03 08:20:00',
                'end_kunjungan' => '2026-02-03 08:50:00',
                'id_siswa_tetap' => 3,
            ],
            [
                'id_kunjungan' => 5,
                'start_kunjungan' => '2026-02-07 11:00:00',
                'end_kunjungan' => '2026-02-07 11:40:00',
                'id_siswa_tetap' => 6,
            ],
            [
                'id_kunjungan' => 6,
                'start_kunjungan' => '2026-02-20 12:15:00',
                'end_kunjungan' => '2026-02-20 12:55:00',
                'id_siswa_tetap' => 5,
            ],
            [
                'id_kunjungan' => 7,
                'start_kunjungan' => '2026-03-05 07:45:00',
                'end_kunjungan' => '2026-03-05 08:15:00',
                'id_siswa_tetap' => 3,
            ],
            [
                'id_kunjungan' => 8,
                'start_kunjungan' => '2026-03-09 09:30:00',
                'end_kunjungan' => '2026-03-09 10:10:00',
                'id_siswa_tetap' => 5,
            ],
            [
                'id_kunjungan' => 9,
                'start_kunjungan' => '2026-03-11 13:00:00',
                'end_kunjungan' => '2026-03-11 13:35:00',
                'id_siswa_tetap' => 6,
            ],
            [
                'id_kunjungan' => 10,
                'start_kunjungan' => '2026-04-02 08:00:00',
                'end_kunjungan' => '2026-04-02 08:30:00',
                'id_siswa_tetap' => 3,
            ],
            [
                'id_kunjungan' => 11,
                'start_kunjungan' => '2026-04-08 10:10:00',
                'end_kunjungan' => '2026-04-08 10:50:00',
                'id_siswa_tetap' => 3,
            ],
            [
                'id_kunjungan' => 12,
                'start_kunjungan' => '2026-04-10 09:45:00',
                'end_kunjungan' => '2026-04-10 10:25:00',
                'id_siswa_tetap' => 5,
            ],
            [
                'id_kunjungan' => 13,
                'start_kunjungan' => '2026-04-14 11:30:00',
                'end_kunjungan' => '2026-04-14 12:00:00',
                'id_siswa_tetap' => 6,
            ],
            [
                'id_kunjungan' => 14,
                'start_kunjungan' => '2026-04-16 12:40:00',
                'end_kunjungan' => '2026-04-16 13:20:00',
                'id_siswa_tetap' => 5,
            ],
        ];

        DB::table('tr_kunjungan_perpus')->upsert(
            $kunjungan,
            ['id_kunjungan'],
            ['start_kunjungan', 'end_kunjungan', 'id_siswa_tetap']
        );
    }
}
