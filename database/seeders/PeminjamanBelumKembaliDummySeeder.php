<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeminjamanBelumKembaliDummySeeder extends Seeder
{
    private const MARKER_PREFIX = '[DUMMY_BELUM_KEMBALI]';

    public function run(): void
    {
        DB::transaction(function () {
            $previousCopyIds = DB::table('tr_peminjaman')
                ->where('KETERANGAN_PEMINJAMAN', 'like', self::MARKER_PREFIX . '%')
                ->pluck('ID_CP_KOLEKSI');

            if ($previousCopyIds->isNotEmpty()) {
                DB::table('cp_koleksi')
                    ->whereIn('ID_CP_KOLEKSI', $previousCopyIds)
                    ->update(['STATUS_BUKU' => 'Tersedia']);
            }

            DB::table('tr_peminjaman')
                ->where('KETERANGAN_PEMINJAMAN', 'like', self::MARKER_PREFIX . '%')
                ->delete();

            $kategoriId = $this->ensureKategori();
            $laporanId = $this->ensureLaporan();
            $siswaList = $this->ensureSiswa();
            $guru = $this->ensureGuru();

            $today = Carbon::today();
            $books = [
                [
                    'ISBN' => '9786230001001',
                    'JUDUL_KOLEKSI' => 'Algoritma Dasar',
                    'PENGARANG' => 'D. Prasetyo',
                    'PENERBIT' => 'Boda Press',
                    'TAHUN' => '2023',
                    'NO_RAK_BUKU' => 'R-01',
                    'borrower' => 'siswa_0',
                    'late_days' => 76,
                    'status' => 'Terlambat',
                ],
                [
                    'ISBN' => '9786230001002',
                    'JUDUL_KOLEKSI' => 'Basis Data SMK',
                    'PENGARANG' => 'M. Lestari',
                    'PENERBIT' => 'Boda Press',
                    'TAHUN' => '2022',
                    'NO_RAK_BUKU' => 'R-02',
                    'borrower' => 'siswa_1',
                    'late_days' => 54,
                    'status' => 'Terlambat',
                ],
                [
                    'ISBN' => '9786230001003',
                    'JUDUL_KOLEKSI' => 'Jaringan Komputer',
                    'PENGARANG' => 'R. Nugroho',
                    'PENERBIT' => 'Tekno Boda',
                    'TAHUN' => '2021',
                    'NO_RAK_BUKU' => 'R-03',
                    'borrower' => 'guru',
                    'late_days' => 41,
                    'status' => 'Dipinjam',
                ],
                [
                    'ISBN' => '9786230001004',
                    'JUDUL_KOLEKSI' => 'Pemrograman Web',
                    'PENGARANG' => 'S. Wibowo',
                    'PENERBIT' => 'Tekno Boda',
                    'TAHUN' => '2024',
                    'NO_RAK_BUKU' => 'R-04',
                    'borrower' => 'siswa_2',
                    'late_days' => 33,
                    'status' => 'Dipinjam',
                ],
                [
                    'ISBN' => '9786230001005',
                    'JUDUL_KOLEKSI' => 'Matematika Terapan',
                    'PENGARANG' => 'A. Safitri',
                    'PENERBIT' => 'Boda Press',
                    'TAHUN' => '2020',
                    'NO_RAK_BUKU' => 'R-05',
                    'borrower' => 'siswa_3',
                    'late_days' => 108,
                    'status' => 'Terlambat',
                ],
            ];

            foreach ($books as $index => $book) {
                DB::table('mst_koleksi_buku')->updateOrInsert(
                    ['ISBN' => $book['ISBN']],
                    [
                        'ID_REF_KOLEKSI' => $kategoriId,
                        'JUDUL_KOLEKSI' => $book['JUDUL_KOLEKSI'],
                        'PENGARANG' => $book['PENGARANG'],
                        'PENERBIT' => $book['PENERBIT'],
                        'TAHUN' => $book['TAHUN'],
                        'NB_KOLEKSI' => 1,
                        'TGL_MASUK_KOLEKSI' => $today->copy()->subMonths(8)->toDateString(),
                        'JUMLAH_EKSEMPLAR' => 1,
                        'JUMLAH_HALAMAN' => 160 + ($index * 12),
                        'UKURAN_BUKU' => 'A5',
                        'BIBLIOGRAFI' => '-',
                        'INDEKS_AWAL_AKHIR' => 0,
                        'KETERANGAN_BUKU' => 'Dummy buku belum kembali',
                        'NO_RAK_BUKU' => $book['NO_RAK_BUKU'],
                        'IS_DELETE' => 0,
                    ]
                );

                $copyId = $this->resolveAvailableCopyId($book['ISBN'], $laporanId);
                $borrower = $this->resolveBorrower($book['borrower'], $siswaList, $guru);
                $dueDate = $today->copy()->subDays($book['late_days']);

                DB::table('tr_peminjaman')->insert([
                    'ID_SISWA_TETAP' => $borrower['ID_SISWA_TETAP'],
                    'ID_CP_KOLEKSI' => $copyId,
                    'NIP_KARYAWAN' => $borrower['NIP_KARYAWAN'],
                    'TGL_PINJAM' => $dueDate->copy()->subDays(7)->toDateString(),
                    'TGL_HARUS_KEMBALI' => $dueDate->toDateString(),
                    'TGL_KEMBALI' => null,
                    'STATUS_PEMINJAMAN' => $book['status'],
                    'KONDISI_BUKU' => 'Baik',
                    'KETERANGAN_PEMINJAMAN' => self::MARKER_PREFIX . ' ' . ($index + 1),
                    'DENDA_PEMINJAMAN' => 0,
                ]);

                DB::table('cp_koleksi')
                    ->where('ID_CP_KOLEKSI', $copyId)
                    ->update(['STATUS_BUKU' => 'Dipinjam']);
            }
        });
    }

    private function ensureKategori(): int
    {
        $kategoriId = DB::table('ref_koleksi')
            ->where('DESKRIPSI_KATEGORI', 'Teknologi')
            ->value('ID_REF_KOLEKSI');

        if ($kategoriId) {
            return (int) $kategoriId;
        }

        return (int) DB::table('ref_koleksi')->insertGetId([
            'NO_KATEGORI_BUKU' => '000',
            'DESKRIPSI_KATEGORI' => 'Teknologi',
            'IS_DELETE' => 0,
        ]);
    }

    private function ensureLaporan(): int
    {
        $laporanId = DB::table('mst_koleksi_laporan')
            ->where('IS_DELETE', 0)
            ->value('ID_MST_LAPORAN');

        if ($laporanId) {
            return (int) $laporanId;
        }

        return (int) DB::table('mst_koleksi_laporan')->insertGetId([
            'ID_PKL_SISWA' => null,
            'IS_DELETE' => 0,
        ]);
    }

    private function ensureSiswa()
    {
        $siswaList = DB::table('mst_siswa')
            ->where('IS_DELETE', 0)
            ->orderBy('ID_SISWA_TETAP')
            ->limit(4)
            ->get(['ID_SISWA_TETAP', 'NAMA_SISWA_TETAP']);

        if ($siswaList->count() >= 4) {
            return $siswaList->values();
        }

        throw new \RuntimeException('Minimal 4 data siswa aktif dibutuhkan. Jalankan DummyDataSeeder terlebih dahulu.');
    }

    private function ensureGuru(): ?object
    {
        return DB::table('mst_karyawan')
            ->where('IS_DELETE', 0)
            ->whereRaw('LOWER(JABATAN_FUNGSIONAL) = ?', ['guru'])
            ->orderBy('NIP_KARYAWAN')
            ->first(['NIP_KARYAWAN', 'NAMA_KARYAWAN']);
    }

    private function resolveAvailableCopyId(string $isbn, int $laporanId): int
    {
        $activeCopyIds = DB::table('tr_peminjaman')
            ->whereNull('TGL_KEMBALI')
            ->whereIn('STATUS_PEMINJAMAN', ['Dipinjam', 'Terlambat'])
            ->pluck('ID_CP_KOLEKSI');

        $copyId = DB::table('cp_koleksi')
            ->where('ISBN', $isbn)
            ->when($activeCopyIds->isNotEmpty(), fn ($query) => $query->whereNotIn('ID_CP_KOLEKSI', $activeCopyIds))
            ->orderBy('ID_CP_KOLEKSI')
            ->value('ID_CP_KOLEKSI');

        if ($copyId) {
            return (int) $copyId;
        }

        return (int) DB::table('cp_koleksi')->insertGetId([
            'ISBN' => $isbn,
            'ID_MST_LAPORAN' => $laporanId,
            'STATUS_BUKU' => 'Tersedia',
        ]);
    }

    private function resolveBorrower(string $borrower, $siswaList, ?object $guru): array
    {
        if ($borrower === 'guru' && $guru) {
            return [
                'ID_SISWA_TETAP' => null,
                'NIP_KARYAWAN' => $guru->NIP_KARYAWAN,
            ];
        }

        $index = (int) str_replace('siswa_', '', $borrower);
        $siswa = $siswaList->get($index) ?? $siswaList->first();

        return [
            'ID_SISWA_TETAP' => $siswa->ID_SISWA_TETAP,
            'NIP_KARYAWAN' => null,
        ];
    }
}
