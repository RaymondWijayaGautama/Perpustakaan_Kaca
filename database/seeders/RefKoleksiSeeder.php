<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefKoleksiSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'ID_REF_KOLEKSI' => 1,
                'NO_KATEGORI_BUKU' => 'BK001',
                'DESKRIPSI_KATEGORI' => 'Karya Umum',
                'IS_DELETE' => 0,
            ],
            [
                'ID_REF_KOLEKSI' => 2,
                'NO_KATEGORI_BUKU' => 'BK002',
                'DESKRIPSI_KATEGORI' => 'Filsafat & Psikologi',
                'IS_DELETE' => 0,
            ],
            [
                'ID_REF_KOLEKSI' => 3,
                'NO_KATEGORI_BUKU' => 'BK003',
                'DESKRIPSI_KATEGORI' => 'Agama',
                'IS_DELETE' => 0,
            ],
            [
                'ID_REF_KOLEKSI' => 11,
                'NO_KATEGORI_BUKU' => 'BK004',
                'DESKRIPSI_KATEGORI' => 'Ilmu Sosial',
                'IS_DELETE' => 0,
            ],
            [
                'ID_REF_KOLEKSI' => 4,
                'NO_KATEGORI_BUKU' => '4',
                'DESKRIPSI_KATEGORI' => 'Laporan PKL',
                'IS_DELETE' => 0,
            ],
            [
                'ID_REF_KOLEKSI' => 5,
                'NO_KATEGORI_BUKU' => 'BK005',
                'DESKRIPSI_KATEGORI' => 'Bahasa',
                'IS_DELETE' => 0,
            ],
            [
                'ID_REF_KOLEKSI' => 6,
                'NO_KATEGORI_BUKU' => 'BK006',
                'DESKRIPSI_KATEGORI' => 'Sains & Matematika',
                'IS_DELETE' => 0,
            ],
            [
                'ID_REF_KOLEKSI' => 7,
                'NO_KATEGORI_BUKU' => 'BK007',
                'DESKRIPSI_KATEGORI' => 'Teknologi',
                'IS_DELETE' => 0,
            ],
            [
                'ID_REF_KOLEKSI' => 8,
                'NO_KATEGORI_BUKU' => 'BK008',
                'DESKRIPSI_KATEGORI' => 'Seni & Rekreasi',
                'IS_DELETE' => 0,
            ],
            [
                'ID_REF_KOLEKSI' => 9,
                'NO_KATEGORI_BUKU' => 'BK009',
                'DESKRIPSI_KATEGORI' => 'Literatur',
                'IS_DELETE' => 0,
            ],
            [
                'ID_REF_KOLEKSI' => 10,
                'NO_KATEGORI_BUKU' => 'BK010',
                'DESKRIPSI_KATEGORI' => 'Sejarah & Geografi',
                'IS_DELETE' => 0,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('ref_koleksi')->updateOrInsert(
                ['ID_REF_KOLEKSI' => $category['ID_REF_KOLEKSI']],
                $category
            );
        }
    }
}
