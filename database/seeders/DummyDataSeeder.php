<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // --- 1. SEEDER KARYAWAN (Admin / Pustakawan & Staf Lainnya) ---
        $karyawanList = [
            [
                'NIP_KARYAWAN' => '19850101201001',
                'NAMA_KARYAWAN' => 'I Made Damar Sadhu Wicaksana',
                'NAMA_LENGKAP_GELAR' => 'I Made Damar Sadhu Wicaksana, S.Kom.',
                'GOLONGAN_KARYAWAN' => 'III/A',
                'JABATAN_FUNGSIONAL' => 'Pustakawan', // Sesuai permintaan (Admin)
                'TANGGAL_MASUK' => '2020-01-01',
                'STATUS_KEPEGAWAIAN' => 'Tetap',
                'NIK_KARYAWAN' => '3404123456780001',
                'TEMPAT_LAHIR_KARYAWAN' => 'Yogyakarta',
                'GENDER_KARYAWAN' => 'Laki-laki',
                'TGL_LAHIR_KARYAWAN' => '1995-05-15',
                'ALAMAT_KARYAWAN' => 'Jl. Babarsari No. 44, Depok, Sleman',
                'NO_HP_KARYAWAN' => '081234567890',
                'EMAIL_KARYAWAN' => 'admin.pustakawan@smkboda.sch.id',
                'PASSWORD_KARYAWAN' => Hash::make('admin123'),
                'PEND_TERAKHIR_KARYAWAN' => 'S1 Teknik Informatika',
                'PRODI_KARYAWAN' => 'Informatika',
                'IS_DELETE' => 0
            ],
            [
                'NIP_KARYAWAN' => '19900202201502',
                'NAMA_KARYAWAN' => 'Siti Nurhaliza',
                'NAMA_LENGKAP_GELAR' => 'Siti Nurhaliza, S.Pd.',
                'GOLONGAN_KARYAWAN' => 'III/B',
                'JABATAN_FUNGSIONAL' => 'Guru',
                'TANGGAL_MASUK' => '2015-07-15',
                'STATUS_KEPEGAWAIAN' => 'Tetap',
                'NIK_KARYAWAN' => '3404123456780002',
                'TEMPAT_LAHIR_KARYAWAN' => 'Bantul',
                'GENDER_KARYAWAN' => 'Perempuan',
                'TGL_LAHIR_KARYAWAN' => '1990-02-02',
                'ALAMAT_KARYAWAN' => 'Jl. Parangtritis Km. 7',
                'NO_HP_KARYAWAN' => '087712345678',
                'EMAIL_KARYAWAN' => 'siti.guru@smkboda.sch.id',
                'PASSWORD_KARYAWAN' => Hash::make('guru123'),
                'PEND_TERAKHIR_KARYAWAN' => 'S1 Pendidikan Komputer',
                'PRODI_KARYAWAN' => 'Pendidikan Teknik Informatika',
                'IS_DELETE' => 0
            ]
        ];

        foreach ($karyawanList as $karyawan) {
            DB::table('mst_karyawan')->updateOrInsert(
                ['NIP_KARYAWAN' => $karyawan['NIP_KARYAWAN']],
                $karyawan
            );
        }

        // --- 2. SEEDER SISWA (5 Data) ---
        $siswaList = [
            ['nama' => 'Budi Santoso', 'gender' => 'Laki-laki', 'nisn' => '0057242951'],
            ['nama' => 'Rifa Amalia', 'gender' => 'Perempuan', 'nisn' => '0064101552'],
            ['nama' => 'Andi Wijaya', 'gender' => 'Laki-laki', 'nisn' => '0055875573'],
            ['nama' => 'Eka Putri', 'gender' => 'Perempuan', 'nisn' => '0057010724'],
            ['nama' => 'Gede Bagus', 'gender' => 'Laki-laki', 'nisn' => '0053630355']
        ];

        foreach ($siswaList as $index => $siswa) {
            // Karena ID_SISWA_TETAP adalah auto-increment, kita tidak mendefinisikannya secara manual 
            // kecuali dibutuhkan untuk sinkronisasi paksa. Kita biarkan DB yang handle.
            DB::table('mst_siswa')->updateOrInsert(
                ['NISN_SISWA' => $siswa['nisn']], // Gunakan NISN sebagai unik key untuk updateOrInsert
                [
                    'NAMA_SISWA_TETAP' => $siswa['nama'],
                    'KODE_CALON_SISWA' => 'REG-2026-' . str_pad($index + 1, 3, "0", STR_PAD_LEFT),
                    'TGL_LAHIR_SISWA' => '2008-0' . rand(1, 9) . '-' . rand(10, 28),
                    'TEMPAT_LAHIR_SISWA' => 'Sleman',
                    'GENDER_SISWA' => $siswa['gender'],
                    'GOLDAR_SISWA' => ['A', 'B', 'AB', 'O'][array_rand(['A', 'B', 'AB', 'O'])],
                    'NO_HP_SISWA' => '0896' . rand(10000000, 99999999),
                    'ALAMAT_JALAN_SISWA' => 'Jl. Gejayan No. ' . rand(1, 100),
                    'RT_SISWA' => '0' . rand(1, 9),
                    'RW_SISWA' => '0' . rand(1, 9),
                    'KECAMATAN_SISWA' => 'Depok',
                    'KOTA_KAB_SISWA' => 'Sleman',
                    'PROVINSI_SISWA' => 'DIY',
                    'NIK_SISWA' => '34041' . rand(10000000000, 99999999999),
                    'TAHUN_LULUS' => '2026',
                    'PASSWORD_SISWA' => Hash::make('siswa123'),
                    'IS_DELETE' => 0
                ]
            );
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}