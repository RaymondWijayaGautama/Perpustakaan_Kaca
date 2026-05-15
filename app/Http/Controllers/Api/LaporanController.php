<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Validator; 
use App\Models\MstKoleksiLaporan;
use App\Models\CpKoleksi; 
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\Siswa;
use Carbon\Carbon;

class LaporanController extends Controller
{
    private function getKategoriLaporanPkl(): ?object
    {
        return DB::table('ref_koleksi')
            ->where('NO_KATEGORI_BUKU', '4')
            ->where(function ($query) {
                $query->where('IS_DELETE', 0)
                    ->orWhereNull('IS_DELETE');
            })
            ->first();
    }

    private function normalizeLaporanTitle(string $title): string
    {
        return mb_strtolower(preg_replace('/\s+/', ' ', trim($title)) ?? trim($title));
    }

    private function findDuplicateLaporanPklTitle(string $title, ?string $ignoreIsbn = null): ?object
    {
        $kategoriLaporan = $this->getKategoriLaporanPkl();

        if (!$kategoriLaporan) {
            return null;
        }

        $normalizedTitle = $this->normalizeLaporanTitle($title);

        return DB::table('mst_koleksi_buku')
            ->where('ID_REF_KOLEKSI', $kategoriLaporan->ID_REF_KOLEKSI)
            ->where('IS_DELETE', 0)
            ->when($ignoreIsbn, fn ($query) => $query->where('ISBN', '!=', $ignoreIsbn))
            ->select('ISBN', 'JUDUL_KOLEKSI')
            ->get()
            ->first(fn ($row) => $this->normalizeLaporanTitle((string) $row->JUDUL_KOLEKSI) === $normalizedTitle);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_koleksi' => 'required|string|max:255',
            'pengarang' => 'required|string|max:100',
            'tahun' => 'required|digits:4',
            'file_laporan' => 'required|file|mimes:pdf,doc,docx|max:10240', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'pesan' => $validator->errors()], 400);
        }

        try {
            $duplicate = $this->findDuplicateLaporanPklTitle($request->judul_koleksi);

            if ($duplicate) {
                return response()->json([
                    'status' => 'error',
                    'pesan' => 'Judul laporan PKL sudah digunakan. Gunakan judul lain agar tidak duplikat.',
                ], 422);
            }

            DB::beginTransaction();

            $isbnPKL = "979" . time() . rand(10, 99); 
            $file = $request->file('file_laporan');
            $namaFile = $isbnPKL . '.' . $file->getClientOriginalExtension(); 
            $file->storeAs('laporan', $namaFile, 'public');

            // Ambil ID_REF_KOLEKSI berdasarkan NO_KATEGORI_BUKU = 4
            $kategoriLaporan = $this->getKategoriLaporanPkl();
            
            $idRefKoleksi = $kategoriLaporan ? $kategoriLaporan->ID_REF_KOLEKSI : null;

            // Gunakan nama kolom sesuai skema perpus_2.sql (huruf besar)
            DB::table('mst_koleksi_buku')->insert([
                'ISBN' => $isbnPKL,
                'ID_REF_KOLEKSI' => $idRefKoleksi, // Menggantikan 'nomor_kategori_buku'
                'JUDUL_KOLEKSI' => $request->judul_koleksi,
                'PENGARANG' => $request->pengarang,
                'PENERBIT' => 'SMK BODA', 
                'TAHUN' => $request->tahun,
                'TGL_MASUK_KOLEKSI' => \Carbon\Carbon::now(),
                'JUMLAH_EKSEMPLAR' => 1,
                'IS_DELETE' => 0,
                'KETERANGAN_BUKU' => $namaFile 
            ]);

            $idLaporanBaru = DB::table('mst_koleksi_laporan')->insertGetId([
                'IS_DELETE' => 0
            ]);

            DB::table('cp_koleksi')->insert([
                'ISBN' => $isbnPKL,
                'STATUS_BUKU' => 'Tersedia',
                'ID_MST_LAPORAN' => $idLaporanBaru
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'pesan' => 'Laporan berhasil ditambahkan!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'pesan' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $isbn)
    {
        $validator = Validator::make($request->all(), [
            'judul_koleksi' => 'required|string|max:255',
            'pengarang' => 'required|string|max:100',
            'tahun' => 'required|digits:4',
            'file_laporan' => 'nullable|file|mimes:pdf,doc,docx|max:10240', 
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'pesan' => $validator->errors()], 400);

        try {
            $buku = DB::table('mst_koleksi_buku')->where('ISBN', $isbn)->first();
            if (!$buku) return response()->json(['status' => 'error', 'pesan' => 'Data tidak ditemukan!'], 404);

            $duplicate = $this->findDuplicateLaporanPklTitle($request->judul_koleksi, $isbn);

            if ($duplicate) {
                return response()->json([
                    'status' => 'error',
                    'pesan' => 'Judul laporan PKL sudah digunakan oleh laporan lain. Gunakan judul lain agar tidak duplikat.',
                ], 422);
            }

            $updateData = [
                'judul_koleksi' => $request->judul_koleksi,
                'pengarang' => $request->pengarang,
                'tahun' => $request->tahun,
            ];

            if ($request->hasFile('file_laporan')) {
                if ($buku->keterangan_buku && Storage::exists('public/laporan/' . $buku->keterangan_buku)) {
                    Storage::delete('public/laporan/' . $buku->keterangan_buku);
                }
                $file = $request->file('file_laporan');
                $namaFile = $isbn . '.' . $file->getClientOriginalExtension(); 
                $file->storeAs('public/laporan', $namaFile);
                $updateData['keterangan_buku'] = $namaFile;
            }

            DB::table('mst_koleksi_buku')->where('ISBN', $isbn)->update($updateData);
            return response()->json(['status' => 'success', 'pesan' => 'Laporan berhasil diubah!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'pesan' => $e->getMessage()], 500);
        }
    }

    public function destroy($isbn)
    {
        try {
            $sedangDipinjam = DB::table('cp_koleksi')
                ->join('tr_peminjaman', 'cp_koleksi.id_cp_koleksi', '=', 'tr_peminjaman.ID_CP_KOLEKSI')
                ->where('cp_koleksi.ISBN', $isbn)
                ->where('tr_peminjaman.STATUS_PEMINJAMAN', 'Dipinjam')
                ->exists();

            if ($sedangDipinjam) {
                return response()->json(['status' => 'error', 'pesan' => 'Gagal! Laporan ini sedang dipinjam oleh siswa.'], 400); 
            }
            DB::table('mst_koleksi_buku')->where('ISBN', $isbn)->update(['is_delete' => 1]);
            return response()->json(['status' => 'success', 'pesan' => 'Berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'pesan' => $e->getMessage()], 500);
        }
    }

    public function laporanPeminjamanGuru(Request $request)
    {
        try {
            return response()->json($this->buildLaporanPeminjamanGuruReport($request));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportPdfPeminjamanGuru(Request $request)
    {
        try {
            $report = $this->buildLaporanPeminjamanGuruReport($request);
            $pdf = Pdf::loadView('laporan.peminjaman_guru_pdf', $report);

            return $pdf->setPaper('a4', 'landscape')
                ->download('Laporan_Peminjaman_Buku_Guru_' . $report['filter']['tahun'] . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function inventarisasiBukuBaru(Request $request)
    {
        try {
            return response()->json($this->buildInventarisasiBukuBaruReport($request));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportPdfInventarisasiBukuBaru(Request $request)
    {
        try {
            $report = $this->buildInventarisasiBukuBaruReport($request);
            $pdf = Pdf::loadView('laporan.inventarisasi_buku_baru_pdf', $report);

            return $pdf->setPaper('a4', 'portrait')
                ->download('Laporan_Inventarisasi_Buku_Baru_' . $report['filter']['tahun'] . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function buildLaporanPeminjamanGuruReport(Request $request): array
    {
        $tahun = (int) ($request->get('tahun', date('Y')));
        $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;

        $query = DB::table('tr_peminjaman as peminjaman')
            ->join('mst_karyawan as guru', 'peminjaman.NIP_KARYAWAN', '=', 'guru.nip_karyawan')
            ->join('cp_koleksi as copy', 'peminjaman.ID_CP_KOLEKSI', '=', 'copy.id_cp_koleksi')
            ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
            ->where('guru.is_delete', 0)
            ->whereRaw('LOWER(guru.jabatan_fungsional) = ?', ['guru'])
            ->where('buku.is_delete', 0)
            ->where('peminjaman.STATUS_PEMINJAMAN', '!=', 'Dihapus')
            ->whereNull('peminjaman.ID_SISWA_TETAP')
            ->whereYear('peminjaman.TGL_PINJAM', $tahun);

        if ($bulan !== null) {
            $query->whereMonth('peminjaman.TGL_PINJAM', $bulan);
        }

        $data = (clone $query)
            ->select(
                'peminjaman.ID_PEMINJAMAN as id_peminjaman',
                'peminjaman.TGL_PINJAM as tgl_peminjaman',
                'peminjaman.TGL_HARUS_KEMBALI as tgl_harus_kembali',
                'peminjaman.TGL_KEMBALI as tgl_kembali',
                'peminjaman.STATUS_PEMINJAMAN as status_peminjaman',
                'guru.nip_karyawan',
                'guru.nama_karyawan as nama_guru',
                'guru.jabatan_fungsional',
                'buku.ISBN',
                'buku.judul_koleksi',
                'buku.pengarang',
                'buku.no_rak_buku'
            )
            ->orderBy('peminjaman.TGL_PINJAM', 'desc')
            ->get()
            ->values();

        $periodeLabel = $bulan !== null
            ? Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y')
            : 'Tahun ' . $tahun;

        return [
            'filter' => [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'periode_label' => $periodeLabel,
            ],
            'summary' => [
                'total_transaksi' => $data->count(),
                'sedang_dipinjam' => $data->where('status_peminjaman', 'Dipinjam')->count(),
                'sudah_kembali' => $data->where('status_peminjaman', 'Kembali')->count(),
                'jumlah_guru' => $data->pluck('nip_karyawan')->unique()->count(),
            ],
            'data' => $data,
        ];
    }

    private function buildInventarisasiBukuBaruReport(Request $request): array
    {
        $tahun = (int) ($request->get('tahun', date('Y')));
        $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;
        $kategoriLaporanPkl = $this->getKategoriLaporanPkl();

        $query = DB::table('mst_koleksi_buku as buku')
            ->join('ref_koleksi as kategori', 'buku.ID_REF_KOLEKSI', '=', 'kategori.ID_REF_KOLEKSI')
            ->where('buku.IS_DELETE', 0)
            ->when($kategoriLaporanPkl, function ($query) use ($kategoriLaporanPkl) {
                $query->where('buku.ID_REF_KOLEKSI', '!=', $kategoriLaporanPkl->ID_REF_KOLEKSI);
            })
            ->where(function ($query) {
                $query->where('kategori.IS_DELETE', 0)
                    ->orWhereNull('kategori.IS_DELETE');
            })
            ->whereNotNull('buku.ISBN')
            ->whereNotNull('buku.JUDUL_KOLEKSI')
            ->where('buku.JUDUL_KOLEKSI', '!=', '')
            ->whereNotNull('buku.PENGARANG')
            ->where('buku.PENGARANG', '!=', '')
            ->whereNotNull('buku.PENERBIT')
            ->where('buku.PENERBIT', '!=', '')
            ->whereNotNull('buku.TGL_MASUK_KOLEKSI')
            ->whereNotNull('buku.ID_REF_KOLEKSI')
            ->whereNotNull('buku.NO_RAK_BUKU')
            ->where('buku.NO_RAK_BUKU', '!=', '')
            ->whereYear('buku.TGL_MASUK_KOLEKSI', $tahun);

        if ($bulan !== null) {
            $query->whereMonth('buku.TGL_MASUK_KOLEKSI', $bulan);
        }

        $books = (clone $query)
            ->select(
                'buku.ISBN',
                'buku.JUDUL_KOLEKSI as judul_koleksi',
                'buku.PENGARANG as pengarang',
                'buku.PENERBIT as penerbit',
                'buku.TAHUN as tahun',
                'buku.TGL_MASUK_KOLEKSI as tgl_masuk_koleksi',
                'buku.NO_RAK_BUKU as no_rak_buku',
                'buku.JUMLAH_EKSEMPLAR as jumlah_eksemplar',
                'kategori.DESKRIPSI_KATEGORI as kategori'
            )
            ->distinct()
            ->orderBy('buku.TGL_MASUK_KOLEKSI', 'desc')
            ->orderBy('buku.JUDUL_KOLEKSI')
            ->get()
            ->values();

        $periodeLabel = $bulan !== null
            ? Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y')
            : 'Tahun ' . $tahun;

        return [
            'filter' => [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'periode_label' => $periodeLabel,
            ],
            'summary' => [
                'total_buku_baru' => $books->count(),
                'total_eksemplar' => (int) $books->sum('jumlah_eksemplar'),
                'total_kategori' => $books->pluck('kategori')->unique()->count(),
            ],
            'data' => $books,
        ];
    }

    public function distribusiKunjunganHari(Request $request)
    {
        try {
            return response()->json($this->buildDistribusiKunjunganHariReport($request));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportPdfDistribusiKunjunganHari(Request $request)
    {
        try {
            $report = $this->buildDistribusiKunjunganHariReport($request);
            $pdf = Pdf::loadView('laporan.distribusi_kunjungan_hari_pdf', $report);

            return $pdf->setPaper('a4', 'portrait')
                ->download('Distribusi_Kunjungan_Hari_' . $report['filter']['tahun'] . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function distribusiKunjunganKelas(Request $request)
    {
        try {
            return response()->json($this->buildDistribusiKunjunganKelasReport($request));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportPdfDistribusiKunjunganKelas(Request $request)
    {
        try {
            $report = $this->buildDistribusiKunjunganKelasReport($request);
            $pdf = Pdf::loadView('laporan.distribusi_kunjungan_kelas_pdf', $report);

            return $pdf->setPaper('a4', 'portrait')
                ->download('Distribusi_Kunjungan_Kelas_' . $report['filter']['tahun'] . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function buildDistribusiKunjunganHariReport(Request $request): array
    {
        $tahun = (int) ($request->get('tahun', date('Y')));
        $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;

        $baseQuery = DB::table('tr_kunjungan_perpus as kunjungan')
            ->join('mst_siswa as siswa', 'kunjungan.id_siswa_tetap', '=', 'siswa.id_siswa_tetap')
            ->where('siswa.is_delete', 0)
            ->whereYear('kunjungan.start_kunjungan', $tahun);

        if ($bulan !== null) {
            $baseQuery->whereMonth('kunjungan.start_kunjungan', $bulan);
        }

        $rows = (clone $baseQuery)
            ->selectRaw('DAYOFWEEK(kunjungan.start_kunjungan) as hari_angka')
            ->selectRaw('COUNT(*) as total_kunjungan')
            ->groupBy('hari_angka')
            ->get()
            ->keyBy('hari_angka');

        $hariMap = [
            2 => 'Senin',
            3 => 'Selasa',
            4 => 'Rabu',
            5 => 'Kamis',
            6 => 'Jumat',
            7 => 'Sabtu',
            1 => 'Minggu',
        ];

        $data = collect($hariMap)->map(function ($label, $angka) use ($rows) {
            $jumlah = (int) optional($rows->get($angka))->total_kunjungan;

            return [
                'hari_angka' => (int) $angka,
                'hari' => $label,
                'jumlah_kunjungan' => $jumlah,
            ];
        })->values();

        $totalKunjungan = $data->sum('jumlah_kunjungan');
        $data = $data->map(function ($item) use ($totalKunjungan) {
            $item['persentase'] = $totalKunjungan > 0
                ? round(($item['jumlah_kunjungan'] / $totalKunjungan) * 100, 2)
                : 0;

            return $item;
        })->values();

        $periodeLabel = $bulan !== null
            ? Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y')
            : 'Tahun ' . $tahun;

        return [
            'filter' => [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'periode_label' => $periodeLabel,
            ],
            'summary' => [
                'total_kunjungan' => $totalKunjungan,
                'hari_aktif' => $data->where('jumlah_kunjungan', '>', 0)->count(),
            ],
            'data' => $data,
        ];
    }

    private function buildDistribusiKunjunganKelasReport(Request $request): array
    {
        $tahun = (int) ($request->get('tahun', date('Y')));
        $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;

        $baseQuery = DB::table('tr_kunjungan_perpus as kunjungan')
            ->join('mst_siswa as siswa', 'kunjungan.id_siswa_tetap', '=', 'siswa.id_siswa_tetap')
            ->where('siswa.is_delete', 0)
            ->whereYear('kunjungan.start_kunjungan', $tahun);

        if ($bulan !== null) {
            $baseQuery->whereMonth('kunjungan.start_kunjungan', $bulan);
        }

        $kelasExpression = "
            CASE
                WHEN CAST(siswa.tahun_lulus AS SIGNED) - ? = 2 THEN 'X'
                WHEN CAST(siswa.tahun_lulus AS SIGNED) - ? = 1 THEN 'XI'
                WHEN CAST(siswa.tahun_lulus AS SIGNED) - ? = 0 THEN 'XII'
                ELSE NULL
            END
        ";

        $rows = (clone $baseQuery)
            ->selectRaw("$kelasExpression as kelas_label", [$tahun, $tahun, $tahun])
            ->selectRaw('COUNT(*) as total_kunjungan')
            ->groupBy('kelas_label')
            ->get();

        $validRows = collect(['X', 'XI', 'XII'])->map(function ($kelas) use ($rows) {
            $match = $rows->firstWhere('kelas_label', $kelas);

            return [
                'kelas' => $kelas,
                'jumlah_kunjungan' => (int) ($match->total_kunjungan ?? 0),
            ];
        });

        $totalValid = $validRows->sum('jumlah_kunjungan');
        $totalSemuaKunjungan = (clone $baseQuery)->count();
        $totalTidakValid = max(0, $totalSemuaKunjungan - $totalValid);

        $data = $validRows->map(function ($item) use ($totalValid) {
            return [
                'kelas' => $item['kelas'],
                'jumlah_kunjungan' => $item['jumlah_kunjungan'],
                'persentase' => $totalValid > 0
                    ? round(($item['jumlah_kunjungan'] / $totalValid) * 100, 2)
                    : 0,
            ];
        })->values();

        $periodeLabel = $bulan !== null
            ? Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y')
            : 'Tahun ' . $tahun;

        return [
            'filter' => [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'periode_label' => $periodeLabel,
            ],
            'summary' => [
                'total_kunjungan_valid' => $totalValid,
                'total_kunjungan_tidak_valid' => $totalTidakValid,
                'jumlah_kelas_aktif' => $data->where('jumlah_kunjungan', '>', 0)->count(),
            ],
            'data' => $data,
        ];
    }

    public function statistikPeminjamanBulanan(Request $request)
    {
        try {
            return response()->json($this->buildStatistikPeminjamanBulananReport($request));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportPdfPeminjamanBulanan(Request $request)
    {
        try {
            $report = $this->buildStatistikPeminjamanBulananReport($request);
            $pdf = Pdf::loadView('laporan.statistik_peminjaman_bulanan_pdf', $report);

            return $pdf->setPaper('a4', 'portrait')
                ->download('Statistik_Peminjaman_Buku_' . $report['filter']['tahun'] . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function buildStatistikPeminjamanBulananReport(Request $request): array
    {
        $tahun = (int) ($request->get('tahun', date('Y')));
        $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;

        $query = DB::table('tr_peminjaman')
            ->whereYear('TGL_PINJAM', $tahun)
            ->where(function ($query) {
                $query->where('STATUS_PEMINJAMAN', '!=', 'Dihapus')
                    ->orWhereNull('STATUS_PEMINJAMAN');
            });

        if ($bulan !== null) {
            $query->whereMonth('TGL_PINJAM', $bulan);
        }

        $rows = (clone $query)
            ->selectRaw('MONTH(TGL_PINJAM) as nomor_bulan')
            ->selectRaw('COUNT(*) as jumlah_peminjaman')
            ->groupBy('nomor_bulan')
            ->pluck('jumlah_peminjaman', 'nomor_bulan');

        $bulanMap = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $bulanRange = $bulan !== null ? [$bulan] : range(1, 12);
        $data = collect($bulanRange)->map(function ($nomorBulan) use ($rows, $bulanMap) {
            return [
                'nomor_bulan' => $nomorBulan,
                'nama_bulan' => $bulanMap[$nomorBulan],
                'jumlah_peminjaman' => (int) ($rows[$nomorBulan] ?? 0),
            ];
        })->values();

        $periodeLabel = $bulan !== null
            ? Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y')
            : 'Tahun ' . $tahun;

        return [
            'filter' => [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'periode_label' => $periodeLabel,
            ],
            'summary' => [
                'total_peminjaman' => (int) $data->sum('jumlah_peminjaman'),
                'jumlah_bulan_aktif' => $data->where('jumlah_peminjaman', '>', 0)->count(),
            ],
            'data' => $data,
        ];
    }

    public function getLaporan(Request $request)
    {
        try {
            // 1. Ambil ID_REF_KOLEKSI yang tepat untuk kategori '4' (laporan)
            $kategoriLaporan = DB::table('ref_koleksi')
                                ->where('NO_KATEGORI_BUKU', '4')
                                ->first();
            
            $idRefKoleksi = $kategoriLaporan ? $kategoriLaporan->ID_REF_KOLEKSI : null;

            // 2. Gunakan ID yang sudah didapat ke dalam query utama
            $query = DB::table('mst_koleksi_buku')
                ->where('ID_REF_KOLEKSI', $idRefKoleksi) // <-- Diperbaiki di sini
                ->where(function($q) {
                    $q->where('IS_DELETE', 0)->orWhereNull('IS_DELETE');
                })
                ->select(
                    'ISBN',
                    'JUDUL_KOLEKSI as judul_koleksi',
                    'PENGARANG as nama_siswa_tetap',
                    'TAHUN as tahun'
                );

            // Filter Judul
            if ($request->filled('judul')) {
                $query->where('JUDUL_KOLEKSI', 'like', '%' . $request->judul . '%');
            }

            // Filter Penulis
            if ($request->filled('penulis')) {
                $query->where('PENGARANG', 'like', '%' . $request->penulis . '%');
            }

            // Eksekusi query
            $data = $query->paginate(5);
            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error Database: ' . $e->getMessage()], 500);
        }
    }

   public function siswaTerajin()
    {
        $siswaTerajin = DB::table('tr_peminjaman as tp')
            ->join('mst_siswa as ms', 'tp.ID_SISWA_TETAP', '=', 'ms.id_siswa_tetap')
            ->leftJoin('hst_kelas as hk', 'ms.id_siswa_tetap', '=', 'hk.ID_SISWA_TETAP')
            ->select(
                'ms.nama_siswa_tetap',
                'ms.nisn_siswa',
                'hk.KELAS as nama_kelas', 
                DB::raw('COUNT(tp.ID_PEMINJAMAN) as peminjaman_count')
            )
            ->groupBy('ms.id_siswa_tetap', 'ms.nama_siswa_tetap', 'ms.nisn_siswa', 'hk.KELAS')
            ->orderBy('peminjaman_count', 'desc')
            ->take(10)
            ->get();

        return response()->json($siswaTerajin);
    }

    public function exportPdfSiswaTerajin()
    {
        $siswaTerajin = DB::table('tr_peminjaman as tp')
            ->join('mst_siswa as ms', 'tp.ID_SISWA_TETAP', '=', 'ms.id_siswa_tetap')
            // Melakukan Left Join ke hst_kelas
            ->leftJoin('hst_kelas as hk', 'ms.id_siswa_tetap', '=', 'hk.ID_SISWA_TETAP')
            ->select(
                'ms.nama_siswa_tetap',
                'ms.nisn_siswa',
                'hk.KELAS as nama_kelas', // Mengambil kolom KELAS dari tabel hst_kelas
                DB::raw('COUNT(tp.ID_PEMINJAMAN) as peminjaman_count')
            )
            ->groupBy('ms.id_siswa_tetap', 'ms.nama_siswa_tetap', 'ms.nisn_siswa', 'hk.KELAS')
            ->orderBy('peminjaman_count', 'desc')
            ->take(10)
            ->get();

        $defaultSiswa = (object)['nama_siswa_tetap' => '-'];
        
        $juara1 = (count($siswaTerajin) >= 1) ? $siswaTerajin[0] : $defaultSiswa;
        $juara2 = (count($siswaTerajin) >= 2) ? $siswaTerajin[1] : $defaultSiswa;
        $juara3 = (count($siswaTerajin) >= 3) ? $siswaTerajin[2] : $defaultSiswa;

        $tahun_ajaran = date('Y') . '/' . (date('Y') + 1);
        $periode = \Carbon\Carbon::now()->translatedFormat('F Y');

        $pdf = Pdf::loadView('laporan.pdf_siswa_terajin', compact(
            'siswaTerajin', 
            'juara1', 
            'juara2', 
            'juara3', 
            'tahun_ajaran', 
            'periode'
        ));
        
        return $pdf->download('Laporan_Siswa_Terajin_Wigaty.pdf');
    }

    public function kunjunganBulanan()
    {
        $laporanKunjungan = DB::table('tr_kunjungan_perpus')
            ->select(
                DB::raw('MONTHNAME(start_kunjungan) as bulan'),
                DB::raw('MONTH(start_kunjungan) as urutan_bulan'),
                DB::raw('COUNT(*) as total_kunjungan')
            )
            ->groupBy('bulan', 'urutan_bulan')
            ->orderBy('urutan_bulan', 'asc')
            ->get();

        return response()->json($laporanKunjungan);
    }
    
    public function exportPdfKunjungan()
    {
        $laporanKunjungan = DB::table('tr_kunjungan_perpus') 
            ->select(
                DB::raw('MONTHNAME(start_kunjungan) as bulan'),
                DB::raw('MONTH(start_kunjungan) as urutan_bulan'),
                DB::raw('COUNT(*) as total_kunjungan')
            )
            ->groupBy('bulan', 'urutan_bulan')
            ->orderBy('urutan_bulan', 'asc')
            ->get();

        $pdf = Pdf::loadView('laporan.kunjungan_bulanan_pdf', compact('laporanKunjungan'));
        
        return $pdf->setPaper('a4', 'portrait')->download('Laporan_Jumlah_Kunjungan_Perpus.pdf');
    }

    public function bukuTerpopuler()
    {
        $tahun = date('Y');

        $laporanBuku = DB::table('tr_peminjaman as tp')
            ->join('cp_koleksi as ck', 'tp.ID_CP_KOLEKSI', '=', 'ck.id_cp_koleksi')
            ->join('mst_koleksi_buku as mkb', 'ck.ISBN', '=', 'mkb.ISBN')
            ->select(
                'mkb.judul_koleksi',
                'mkb.ISBN',
                'mkb.pengarang',
                DB::raw('COUNT(tp.ID_PEMINJAMAN) as total_dipinjam')
            )
            ->whereYear('tp.TGL_PINJAM', $tahun)
            ->groupBy('mkb.ISBN', 'mkb.judul_koleksi', 'mkb.pengarang')
            ->orderBy('total_dipinjam', 'desc')
            ->take(10) 
            ->get();

        return response()->json($laporanBuku);
    }

    public function exportPdfBukuTerpopuler()
    {
        $tahun = date('Y');

        $laporanBuku = DB::table('tr_peminjaman as tp')
            ->join('cp_koleksi as ck', 'tp.ID_CP_KOLEKSI', '=', 'ck.id_cp_koleksi')
            ->join('mst_koleksi_buku as mkb', 'ck.ISBN', '=', 'mkb.ISBN')
            ->select(
                'mkb.judul_koleksi',
                'mkb.ISBN',
                'mkb.pengarang',
                DB::raw('COUNT(tp.ID_PEMINJAMAN) as total_dipinjam')
            )
            ->whereYear('tp.TGL_PINJAM', $tahun)
            ->groupBy('mkb.ISBN', 'mkb.judul_koleksi', 'mkb.pengarang')
            ->orderBy('total_dipinjam', 'desc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.buku_terpopuler_pdf', compact('laporanBuku', 'tahun'));
        return $pdf->download('Laporan_Buku_Terpopuler_'.$tahun.'.pdf');
    }

    public function kategoriPopuler()
    {
        $tahun = date('Y');

        $laporanKategori = DB::table('tr_peminjaman as tp')
            ->join('cp_koleksi as ck', 'tp.ID_CP_KOLEKSI', '=', 'ck.id_cp_koleksi')
            ->join('mst_koleksi_buku as mkb', 'ck.ISBN', '=', 'mkb.ISBN')
            ->join('ref_koleksi as rk', 'mkb.id_ref_koleksi', '=', 'rk.id_ref_koleksi')
            ->select('rk.DESKRIPSI_KATEGORI', DB::raw('COUNT(tp.ID_PEMINJAMAN) as total_dipinjam'))
            ->whereYear('tp.TGL_PINJAM', $tahun)
            ->groupBy('rk.id_ref_koleksi', 'rk.DESKRIPSI_KATEGORI')
            ->orderBy('total_dipinjam', 'desc')
            ->get();

        return response()->json($laporanKategori);
    }

    public function exportPdfKategori()
    {
        $tahun = date('Y');

        $laporanKategori = DB::table('tr_peminjaman as tp')
            ->join('cp_koleksi as ck', 'tp.ID_CP_KOLEKSI', '=', 'ck.id_cp_koleksi')
            ->join('mst_koleksi_buku as mkb', 'ck.ISBN', '=', 'mkb.ISBN')
            ->join('ref_koleksi as rk', 'mkb.id_ref_koleksi', '=', 'rk.id_ref_koleksi')
            ->select('rk.DESKRIPSI_KATEGORI', DB::raw('COUNT(tp.ID_PEMINJAMAN) as total_dipinjam'))
            ->whereYear('tp.TGL_PINJAM', $tahun)
            ->groupBy('rk.id_ref_koleksi', 'rk.DESKRIPSI_KATEGORI')
            ->orderBy('total_dipinjam', 'desc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.kategori_populer_pdf', compact('laporanKategori', 'tahun'));
        return $pdf->download('Laporan_Kategori_Terpopuler_'.$tahun.'.pdf');
    }

    public function statistikKunjungan()
    {
        $tahun = date('Y');

        $laporanKunjungan = DB::table('tr_kunjungan_perpus')
            ->select(
                DB::raw('MONTHNAME(start_kunjungan) as bulan'),
                DB::raw('MONTH(start_kunjungan) as urutan_bulan'),
                DB::raw('COUNT(*) as total_kunjungan')
            )
            ->whereYear('start_kunjungan', $tahun)
            ->groupByRaw('MONTH(start_kunjungan), MONTHNAME(start_kunjungan)')
            ->orderBy('urutan_bulan', 'asc')
            ->get();

        return view('laporan.statistik_kunjungan', compact('laporanKunjungan', 'tahun'));
    }

    public function exportPdfStatistikKunjungan()
    {
        $tahun = date('Y');

        $laporanKunjungan = DB::table('tr_kunjungan_perpus')
            ->select(
                DB::raw('MONTHNAME(start_kunjungan) as bulan'),
                DB::raw('MONTH(start_kunjungan) as urutan_bulan'),
                DB::raw('COUNT(*) as total_kunjungan')
            )
            ->whereYear('start_kunjungan', $tahun)
            ->groupByRaw('MONTH(start_kunjungan), MONTHNAME(start_kunjungan)')
            ->orderBy('urutan_bulan', 'asc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.statistik_kunjungan_pdf', compact('laporanKunjungan', 'tahun'));
        return $pdf->download('Statistik_Kunjungan_'.$tahun.'.pdf');
    }


    public function downloadLaporan($isbn)
    {
        try {
            // Cari data laporan berdasarkan ISBN
            $laporan = DB::table('mst_koleksi_buku')
                        ->where('ISBN', $isbn)
                        ->where('IS_DELETE', 0)
                        ->first();

            if (!$laporan) {
                return response()->json([
                    'status' => 'error', 
                    'pesan' => 'Data laporan tidak ditemukan.'
                ], 404);
            }

            if (!$laporan->KETERANGAN_BUKU) {
                return response()->json([
                    'status' => 'error', 
                    'pesan' => 'File tidak ditemukan di database.'
                ], 404);
            }

            $pathToFile = storage_path('app/public/laporan/' . $laporan->KETERANGAN_BUKU);

            if (!file_exists($pathToFile)) {
                return response()->json([
                    'status' => 'error', 
                    'pesan' => 'File fisik tidak ditemukan di server.'
                ], 404);
            }

            $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
            $filename = $laporan->JUDUL_KOLEKSI . '.' . $extension;
            
            return response()->download($pathToFile, $filename, [
                'Content-Type' => mime_content_type($pathToFile),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    

    }

    public function StatistikKunjunganBulanan(Request $request)
    {
        try {
            // Tangkap parameter tahun, jika kosong gunakan tahun saat ini
            $tahun = (int) $request->get('tahun', date('Y'));

            // Query untuk menghitung total kunjungan yang di-group per bulan
            $kunjungan = DB::table('tr_kunjungan_perpus')
                ->selectRaw('MONTH(start_kunjungan) as bulan, COUNT(*) as total')
                ->whereYear('start_kunjungan', $tahun)
                ->groupByRaw('MONTH(start_kunjungan)')
                ->pluck('total', 'bulan'); // Formatnya jadi array [1 => 10, 2 => 15, ...]

            // Daftar bulan statis untuk sumbu X di chart
            $namaBulan = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agt',
                9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];

            $dataChart = [];
            // Looping wajib 1-12 supaya chart tetap menampilkan bulan penuh walau kunjungannya 0
            for ($i = 1; $i <= 12; $i++) {
                $dataChart[] = [
                    'bulan' => $namaBulan[$i],
                    'total' => $kunjungan->has($i) ? $kunjungan[$i] : 0
                ];
            }

            return response()->json([
                'status' => 'success',
                'filter' => [
                    'tahun' => $tahun
                ],
                'data' => $dataChart
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'pesan' => 'Gagal mengambil data statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdfStatistikKunjunganBulanan(Request $request)
    {
        try {
            $tahun = (int) $request->get('tahun', date('Y'));

            // Ambil data kunjungan bulanan
            $kunjungan = DB::table('tr_kunjungan_perpus')
                ->selectRaw('MONTH(start_kunjungan) as nomor_bulan, COUNT(*) as total')
                ->whereYear('start_kunjungan', $tahun)
                ->groupByRaw('MONTH(start_kunjungan)')
                ->pluck('total', 'nomor_bulan');

            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $dataLaporan = [];
            $totalSetahun = 0;

            for ($i = 1; $i <= 12; $i++) {
                $jumlah = $kunjungan->has($i) ? $kunjungan[$i] : 0;
                $dataLaporan[] = [
                    'bulan' => $namaBulan[$i],
                    'jumlah' => $jumlah
                ];
                $totalSetahun += $jumlah;
            }

            // Load view blade dan jadikan PDF
            $pdf = Pdf::loadView('laporan.statistik_kunjungan_bulanan_pdf', compact('dataLaporan', 'tahun', 'totalSetahun'));
            
            // Set kertas A4 portrait
            return $pdf->setPaper('a4', 'portrait')->download('Statistik_Kunjungan_'.$tahun.'.pdf');

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // --- 1. METHOD UNTUK GRAFIK REACT ---
    public function statistikPeminjamanKelas(Request $request)
    {
        try {
            $tahun = (int) $request->get('tahun', date('Y'));
            $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;

            $query = DB::table('tr_peminjaman as tp')
                ->join('hst_kelas as hk', 'tp.ID_SISWA_TETAP', '=', 'hk.ID_SISWA_TETAP')
                ->select('hk.KELAS as kelas', DB::raw('COUNT(DISTINCT tp.ID_PEMINJAMAN) as total'))
                ->whereYear('tp.TGL_PINJAM', $tahun);

            if ($bulan !== null) {
                $query->whereMonth('tp.TGL_PINJAM', $bulan);
            }

            $dataChart = $query->whereNotNull('hk.KELAS')
                ->where('hk.KELAS', '!=', '')
                ->groupBy('hk.KELAS')
                ->orderBy('hk.KELAS', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'filter' => [
                    'tahun' => $tahun,
                    'bulan' => $bulan
                ],
                'data' => $dataChart
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'pesan' => 'Gagal mengambil data statistik kelas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdfPeminjamanKelas(Request $request)
    {
        try {
            $tahun = (int) $request->get('tahun', date('Y'));
            $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;

            $query = DB::table('tr_peminjaman as tp')
                ->join('hst_kelas as hk', 'tp.ID_SISWA_TETAP', '=', 'hk.ID_SISWA_TETAP')
                ->select('hk.KELAS as kelas', DB::raw('COUNT(DISTINCT tp.ID_PEMINJAMAN) as total'))
                ->whereYear('tp.TGL_PINJAM', $tahun);

            if ($bulan !== null) {
                $query->whereMonth('tp.TGL_PINJAM', $bulan);
            }

            $dataLaporan = $query->whereNotNull('hk.KELAS')
                ->where('hk.KELAS', '!=', '')
                ->groupBy('hk.KELAS')
                ->orderBy('hk.KELAS', 'asc')
                ->get();

            $totalSemua = $dataLaporan->sum('total');
            
            $periodeLabel = $bulan 
                ? \Carbon\Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y') 
                : 'Tahun ' . $tahun;

            $pdf = Pdf::loadView('laporan.peminjaman_kelas_pdf', compact('dataLaporan', 'periodeLabel', 'totalSemua'));
            
            return $pdf->setPaper('a4', 'portrait')->download('Laporan_Peminjaman_Kelas_'.$tahun.'.pdf');

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}   
