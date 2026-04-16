<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BukuBaruDummySeeder extends Seeder
{
    public function run(): void
    {
        $dataBukuBaru = [
            [
                'ISBN' => '978-602-03-7777-1',
                'judul_koleksi' => 'Dasar-Dasar UI UX',
                'pengarang' => 'Rina Oktavia',
                'penerbit' => 'Informatika',
                'tahun' => '2026',
                'nb_koleksi' => 501,
                'tgl_masuk_koleksi' => '2026-04-10',
                'jumlah_ekslempar' => 12,
                'jumlah_halaman' => 210,
                'ukuran_buku' => '17x24 cm',
                'bibliografi' => 'Ada',
                'indeks_awal_akhir' => 1,
                'keterangan_buku' => 'Buku baru inventaris April',
                'no_rak_buku' => 'T-08',
                'is_delete' => 0,
                'id_ref_koleksi' => 3,
            ],
            [
                'ISBN' => '978-602-03-7777-2',
                'judul_koleksi' => 'Akuntansi Dasar SMK',
                'pengarang' => 'Dewi Lestari',
                'penerbit' => 'Erlangga',
                'tahun' => '2026',
                'nb_koleksi' => 502,
                'tgl_masuk_koleksi' => '2026-04-12',
                'jumlah_ekslempar' => 18,
                'jumlah_halaman' => 250,
                'ukuran_buku' => '21x29 cm',
                'bibliografi' => '-',
                'indeks_awal_akhir' => 0,
                'keterangan_buku' => 'Buku pelajaran baru semester genap',
                'no_rak_buku' => 'A-03',
                'is_delete' => 0,
                'id_ref_koleksi' => 1,
            ],
            [
                'ISBN' => '978-602-03-7777-3',
                'judul_koleksi' => 'Cerita Nusantara Modern',
                'pengarang' => 'Ahmad Fauzi',
                'penerbit' => 'Gramedia',
                'tahun' => '2025',
                'nb_koleksi' => 503,
                'tgl_masuk_koleksi' => '2026-04-15',
                'jumlah_ekslempar' => 9,
                'jumlah_halaman' => 180,
                'ukuran_buku' => '14x20 cm',
                'bibliografi' => 'Ada',
                'indeks_awal_akhir' => 1,
                'keterangan_buku' => 'Koleksi fiksi baru',
                'no_rak_buku' => 'F-15',
                'is_delete' => 0,
                'id_ref_koleksi' => 2,
            ],
        ];

        DB::table('mst_koleksi_buku')->upsert(
            $dataBukuBaru,
            ['ISBN'],
            [
                'judul_koleksi',
                'pengarang',
                'penerbit',
                'tahun',
                'nb_koleksi',
                'tgl_masuk_koleksi',
                'jumlah_ekslempar',
                'jumlah_halaman',
                'ukuran_buku',
                'bibliografi',
                'indeks_awal_akhir',
                'keterangan_buku',
                'no_rak_buku',
                'is_delete',
                'id_ref_koleksi',
            ]
        );
    }
}
