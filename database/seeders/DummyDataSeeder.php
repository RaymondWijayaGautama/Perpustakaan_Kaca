<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Daftar Nama Siswa Asli untuk digunakan di Anggota & Penulis PKL
        $namaSiswaList = [
            'Damar Wicaksana', 'Rifa Amalia', 'Budi Santoso', 'Siti Aminah', 
            'Andi Wijaya', 'Eka Putri', 'Gede Bagus', 'Luh Putu', 
            'Aditya Pratama', 'Rizky Ramadhan'
        ];

        // 1. DATA REFERENSI KATEGORI (Tetap)
        $kategori = [
            ['id_ref_koleksi' => 1, 'deskripsi_kategori' => 'Buku Pelajaran', 'is_delete' => 0],
            ['id_ref_koleksi' => 2, 'deskripsi_kategori' => 'Novel & Fiksi', 'is_delete' => 0],
            ['id_ref_koleksi' => 3, 'deskripsi_kategori' => 'Teknologi Informasi', 'is_delete' => 0],
            ['id_ref_koleksi' => 4, 'deskripsi_kategori' => 'Laporan PKL', 'is_delete' => 0],
            ['id_ref_koleksi' => 5, 'deskripsi_kategori' => 'Sains & Alam', 'is_delete' => 0],
            ['id_ref_koleksi' => 6, 'deskripsi_kategori' => 'Sejarah & Budaya', 'is_delete' => 0],
            ['id_ref_koleksi' => 7, 'deskripsi_kategori' => 'Seni & Desain', 'is_delete' => 0],
            ['id_ref_koleksi' => 8, 'deskripsi_kategori' => 'Biografi', 'is_delete' => 0],
            ['id_ref_koleksi' => 9, 'deskripsi_kategori' => 'Komik & Manga', 'is_delete' => 0],
            ['id_ref_koleksi' => 10, 'deskripsi_kategori' => 'Ensiklopedia', 'is_delete' => 0],
        ];
        foreach ($kategori as $k) {
            DB::table('ref_koleksi')->updateOrInsert(['id_ref_koleksi' => $k['id_ref_koleksi']], $k);
        }

        // 2. DATA KARYAWAN (Admin Utama)
        $adminNip = '19850101201001';
        DB::table('mst_karyawan')->updateOrInsert(['nip_karyawan' => $adminNip], [
            'nama_karyawan' => 'Budi Pustakawan',
            'nama_lengkap_gelar' => 'Budi S.Kom',
            'golongan_karyawan' => 'III/A',
            'jabatan_fungsional' => 'Pustakawan',
            'tanggal_masuk' => '2020-01-01',
            'status_kepegawaian' => 'Tetap',
            'nik_karyawan' => '1234567890123456',
            'tempat_lahir_karyawan' => 'Jakarta',
            'gender_karyawan' => 'Laki-laki',
            'tgl_lahir_karyawan' => '1990-01-01',
            'alamat_karyawan' => 'Jl. Merdeka No. 10',
            'no_hp_karyawan' => '08123456789',
            'email_karyawan' => 'pustakawan@perpus.com',
            'password_karyawan' => Hash::make('admin123'),
            'pend_terakhir_karyawan' => 'S1 Teknik Informatika',
            'prodi_karyawan' => 'Informatika',
            'sertifikat_pendidik' => '-',
            'link_foto_karyawan' => 'default.jpg',
            'is_delete' => 0
        ]);

        // 3. DATA SISWA (MENGGUNAKAN NAMA ASLI)
        $nisnList = ['0057242952', '0064101551', '0055875579', '0057010721', '0053630354', '0045373919', '0052372577', '0058464857', '0043703466', '0059055438'];
        foreach ($nisnList as $index => $nisn) {
            $idSiswa = $index + 1;
            DB::table('mst_siswa')->updateOrInsert(['id_siswa_tetap' => $idSiswa], [
                'nama_siswa_tetap' => $namaSiswaList[$index], // Mengambil dari array nama asli
                'nisn_siswa' => $nisn,
                'kode_calon_siswa' => "REG-2024-" . str_pad($idSiswa, 3, "0", STR_PAD_LEFT),
                'tgl_lahir_siswa' => '2007-01-01',
                'tempat_lahir_siswa' => 'Yogyakarta',
                'gender_siswa' => $index % 2 == 0 ? 'Laki-laki' : 'Perempuan',
                'goldar_siswa' => 'A',
                'no_hp_siswa' => '0877' . rand(1000, 9999),
                'alamat_jalan_siswa' => "Dusun $idSiswa, Sleman",
                'rt_siswa' => '01', 'rw_siswa' => '01',
                'kelurahan_siswa' => 1, 'kecamatan_siswa' => 'Depok', 'kota_kab_siswa' => 'Sleman',
                'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55281', 'nik_siswa' => '34041' . rand(100, 999) . $idSiswa,
                'nama_ayah_siswa' => 'Wali Damar Wicaksana',
                'tahun_lulus' => '2025',
                'password_siswa' => Hash::make('siswa123'),
                'is_delete' => 0
            ]);
        }

        // 4. MASTER BUKU UMUM (DENGAN FIELD LENGKAP)
        $bukuData = ["Clean Code", "Refactoring", "Design Patterns", "The Pragmatic Programmer", "Intro to Algorithm", "Deep Learning", "Docker Deep Dive", "Kubernetes Up & Run", "Modern PHP", "JS: The Good Parts"];
        for ($i = 1; $i <= 10; $i++) {
            $isbn = "9786020000" . str_pad((string) $i, 3, "0", STR_PAD_LEFT);
            $tahunTerbit = rand(2015, 2024);
            DB::table('mst_koleksi_buku')->updateOrInsert(['ISBN' => $isbn], [
                'judul_koleksi' => $bukuData[$i-1],
                'pengarang' => "Penulis Ahli $i",
                'penerbit' => "Penerbit Utama $i",
                'tahun' => (string)$tahunTerbit,
                'nb_koleksi' => 100 + $i,
                'tgl_masuk_koleksi' => Carbon::now(),
                'jumlah_eksemplar' => 5, // FIXED: Typo diperbaiki jadi jumlah_eksemplar
                'jumlah_halaman' => 250,
                'ukuran_buku' => 'A5',
                'id_ref_koleksi' => rand(1, 3), 
                'is_delete' => 0,
                'bibliografi' => '-',
                'indeks_awal_akhir' => 0,
                'keterangan_buku' => 'Koleksi Umum',
                'no_rak_buku' => 'RAK-' . chr(64 + $i)
            ]);
            DB::table('cp_koleksi')->updateOrInsert(['id_cp_koleksi' => 100 + $i], [
                'status_buku' => 'Tersedia', 'ISBN' => $isbn, 'id_mst_laporan' => null
            ]);
        }

        // 5. MASTER LAPORAN PKL (MENGGUNAKAN NAMA SISWA DARI ANGGOTA)
        for ($i = 1; $i <= 10; $i++) {
            $isbnPKL = "9796028000" . str_pad((string) $i, 3, "0", STR_PAD_LEFT);
            $namaPenulis = $namaSiswaList[$i-1]; // Siswa Anggota adalah Penulisnya

            DB::table('mst_koleksi_laporan')->updateOrInsert(['id_mst_laporan' => $i], ['is_delete' => 0]);
            DB::table('mst_koleksi_buku')->updateOrInsert(['ISBN' => $isbnPKL], [
                'judul_koleksi' => "Laporan PKL Siswa $i",
                'pengarang' => $namaPenulis, // Nama manusia dari anggota
                'penerbit' => 'SMK BODA Yogyakarta',
                'tahun' => '2024',
                'id_ref_koleksi' => 4,
                'nb_koleksi' => 400 + $i,
                'tgl_masuk_koleksi' => Carbon::now(),
                'jumlah_eksemplar' => 1, // FIXED: Typo diperbaiki
                'jumlah_halaman' => 80,
                'ukuran_buku' => 'A4',
                'is_delete' => 0,
                'bibliografi' => 'Ada',
                'indeks_awal_akhir' => 0,
                'keterangan_buku' => 'Arsip PKL',
                'no_rak_buku' => 'RAK-PKL'
            ]);
            DB::table('cp_koleksi')->updateOrInsert(['id_cp_koleksi' => 200 + $i], [
                'status_buku' => 'Tersedia', 'ISBN' => $isbnPKL, 'id_mst_laporan' => $i
            ]);
        }

        // 6. TRANSAKSI PEMINJAMAN
        for ($i = 1; $i <= 5; $i++) {
            DB::table('tr_peminjaman')->updateOrInsert(['id_peminjaman' => 1000 + $i], [
                'tgl_pinjam' => Carbon::now()->subDays(5), // FIXED: tgl_peminjaman jadi tgl_pinjam
                'tgl_harus_kembali' => Carbon::now()->addDays(2),
                'tgl_kembali' => null,
                'status_peminjaman' => 'Dipinjam',
                'kondisi_buku' => 'Baik',
                'keterangan_peminjaman' => 'Peminjaman Aktif',
                'denda_peminjaman' => 0,
                'id_cp_koleksi' => 100 + $i,
                'id_siswa_tetap' => $i,
                'nip_karyawan' => $adminNip
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}