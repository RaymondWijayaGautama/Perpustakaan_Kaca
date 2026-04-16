<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeminjamanGuruDummySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $guruList = DB::table('mst_karyawan')
                ->whereRaw('LOWER(jabatan_fungsional) = ?', ['guru'])
                ->orderBy('nip_karyawan')
                ->get(['nip_karyawan', 'nama_karyawan']);

            $siswa = DB::table('mst_siswa')
                ->orderBy('id_siswa_tetap')
                ->first(['id_siswa_tetap', 'nama_siswa_tetap']);

            $laporanId = DB::table('mst_koleksi_laporan')
                ->orderBy('id_mst_laporan')
                ->value('id_mst_laporan');

            if ($guruList->count() < 1 || !$siswa || !$laporanId) {
                throw new \RuntimeException('Data guru, siswa, atau master laporan belum tersedia untuk membuat dummy transaksi guru.');
            }

            $availableCopies = DB::table('cp_koleksi as cp')
                ->join('mst_koleksi_buku as buku', 'buku.ISBN', '=', 'cp.ISBN')
                ->leftJoin('tr_peminjaman as pinjam', function ($join) {
                    $join->on('pinjam.id_cp_koleksi', '=', 'cp.id_cp_koleksi')
                        ->whereIn('pinjam.status_peminjaman', ['Dipinjam', 'Terlambat'])
                        ->whereNull('pinjam.tgl_kembali');
                })
                ->where('buku.is_delete', 0)
                ->where('buku.id_ref_koleksi', '!=', 4)
                ->whereNull('pinjam.id_peminjaman')
                ->orderBy('cp.id_cp_koleksi')
                ->get([
                    'cp.id_cp_koleksi',
                    'cp.ISBN',
                    'buku.judul_koleksi',
                ]);

            if ($availableCopies->count() < 4) {
                $bookTargets = DB::table('mst_koleksi_buku')
                    ->where('is_delete', 0)
                    ->where('id_ref_koleksi', '!=', 4)
                    ->orderBy('tgl_masuk_koleksi', 'desc')
                    ->orderBy('ISBN')
                    ->limit(4)
                    ->get(['ISBN']);

                $nextCopyId = (int) DB::table('cp_koleksi')->max('id_cp_koleksi');

                foreach ($bookTargets as $book) {
                    $exists = DB::table('cp_koleksi')
                        ->where('ISBN', $book->ISBN)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    $nextCopyId++;

                    DB::table('cp_koleksi')->insert([
                        'id_cp_koleksi' => $nextCopyId,
                        'status_buku' => 'Tersedia',
                        'ISBN' => $book->ISBN,
                        'id_mst_laporan' => $laporanId,
                    ]);
                }

                $availableCopies = DB::table('cp_koleksi as cp')
                    ->join('mst_koleksi_buku as buku', 'buku.ISBN', '=', 'cp.ISBN')
                    ->leftJoin('tr_peminjaman as pinjam', function ($join) {
                        $join->on('pinjam.id_cp_koleksi', '=', 'cp.id_cp_koleksi')
                            ->whereIn('pinjam.status_peminjaman', ['Dipinjam', 'Terlambat'])
                            ->whereNull('pinjam.tgl_kembali');
                    })
                    ->where('buku.is_delete', 0)
                    ->where('buku.id_ref_koleksi', '!=', 4)
                    ->whereNull('pinjam.id_peminjaman')
                    ->orderBy('cp.id_cp_koleksi')
                    ->get([
                        'cp.id_cp_koleksi',
                        'cp.ISBN',
                        'buku.judul_koleksi',
                    ]);
            }

            if ($availableCopies->count() < 4) {
                throw new \RuntimeException('Copy buku tersedia untuk dummy transaksi guru belum cukup. Minimal 4 copy dibutuhkan.');
            }

            $markers = [
                '[DUMMY_GURU_1]',
                '[DUMMY_GURU_2]',
                '[DUMMY_GURU_3]',
                '[DUMMY_GURU_4]',
            ];

            $previousDummyCopies = DB::table('tr_peminjaman')
                ->whereIn('keterangan_peminjaman', $markers)
                ->pluck('id_cp_koleksi');

            if ($previousDummyCopies->isNotEmpty()) {
                DB::table('cp_koleksi')
                    ->whereIn('id_cp_koleksi', $previousDummyCopies)
                    ->update(['status_buku' => 'Tersedia']);
            }

            DB::table('tr_peminjaman')
                ->whereIn('keterangan_peminjaman', $markers)
                ->delete();

            $guruA = $guruList->get(0);
            $guruB = $guruList->get(1) ?? $guruList->get(0);
            $guruC = $guruList->get(2) ?? $guruList->get(0);

            $copyA = $availableCopies->get(0);
            $copyB = $availableCopies->get(1);
            $copyC = $availableCopies->get(2);
            $copyD = $availableCopies->get(3);

            $baseId = (int) DB::table('tr_peminjaman')->max('id_peminjaman');

            $rows = [
                [
                    'id_peminjaman' => $baseId + 1,
                    'tgl_peminjaman' => '2026-04-03',
                    'tgl_harus_kembali' => '2026-04-10',
                    'tgl_kembali' => '2026-04-09',
                    'status_peminjaman' => 'Kembali',
                    'kondisi_buku' => 'Baik',
                    'keterangan_peminjaman' => '[DUMMY_GURU_1]',
                    'denda_peminjaman' => 0,
                    'id_cp_koleksi' => $copyA->id_cp_koleksi,
                    'id_siswa_tetap' => $siswa->id_siswa_tetap,
                    'nip_karyawan' => $guruA->nip_karyawan,
                ],
                [
                    'id_peminjaman' => $baseId + 2,
                    'tgl_peminjaman' => '2026-04-07',
                    'tgl_harus_kembali' => '2026-04-14',
                    'tgl_kembali' => null,
                    'status_peminjaman' => 'Dipinjam',
                    'kondisi_buku' => 'Baik',
                    'keterangan_peminjaman' => '[DUMMY_GURU_2]',
                    'denda_peminjaman' => 0,
                    'id_cp_koleksi' => $copyB->id_cp_koleksi,
                    'id_siswa_tetap' => $siswa->id_siswa_tetap,
                    'nip_karyawan' => $guruB->nip_karyawan,
                ],
                [
                    'id_peminjaman' => $baseId + 3,
                    'tgl_peminjaman' => '2026-04-11',
                    'tgl_harus_kembali' => '2026-04-18',
                    'tgl_kembali' => '2026-04-16',
                    'status_peminjaman' => 'Kembali',
                    'kondisi_buku' => 'Baik',
                    'keterangan_peminjaman' => '[DUMMY_GURU_3]',
                    'denda_peminjaman' => 0,
                    'id_cp_koleksi' => $copyC->id_cp_koleksi,
                    'id_siswa_tetap' => $siswa->id_siswa_tetap,
                    'nip_karyawan' => $guruC->nip_karyawan,
                ],
                [
                    'id_peminjaman' => $baseId + 4,
                    'tgl_peminjaman' => '2026-04-15',
                    'tgl_harus_kembali' => '2026-04-22',
                    'tgl_kembali' => null,
                    'status_peminjaman' => 'Dipinjam',
                    'kondisi_buku' => 'Baik',
                    'keterangan_peminjaman' => '[DUMMY_GURU_4]',
                    'denda_peminjaman' => 0,
                    'id_cp_koleksi' => $copyD->id_cp_koleksi,
                    'id_siswa_tetap' => $siswa->id_siswa_tetap,
                    'nip_karyawan' => $guruA->nip_karyawan,
                ],
            ];

            DB::table('tr_peminjaman')->insert($rows);

            DB::table('cp_koleksi')
                ->whereIn('id_cp_koleksi', [$copyA->id_cp_koleksi, $copyC->id_cp_koleksi])
                ->update(['status_buku' => 'Tersedia']);

            DB::table('cp_koleksi')
                ->whereIn('id_cp_koleksi', [$copyB->id_cp_koleksi, $copyD->id_cp_koleksi])
                ->update(['status_buku' => 'Dipinjam']);
        });
    }
}
