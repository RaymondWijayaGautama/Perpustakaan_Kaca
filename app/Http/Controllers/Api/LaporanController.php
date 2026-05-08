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
            DB::beginTransaction();

            $isbnPKL = "979" . time() . rand(10, 99); 
            $file = $request->file('file_laporan');
            $namaFile = $isbnPKL . '.' . $file->getClientOriginalExtension(); 
            $file->storeAs('public/laporan', $namaFile);

            DB::table('mst_koleksi_buku')->insert([
                'ISBN' => $isbnPKL,
                'judul_koleksi' => $request->judul_koleksi,
                'pengarang' => $request->pengarang,
                'penerbit' => 'SMK BODA', 
                'tahun' => $request->tahun,
                'id_ref_koleksi' => 4,
                'tgl_masuk_koleksi' => Carbon::now(),
                'jumlah_eksemplar' => 1,
                'is_delete' => 0,
                'keterangan_buku' => $namaFile 
            ]);

            $idLaporanBaru = DB::table('mst_koleksi_laporan')->insertGetId(['is_delete' => 0]);
            DB::table('cp_koleksi')->insert([
                'ISBN' => $isbnPKL,
                'status_buku' => 'Tersedia',
                'id_mst_laporan' => $idLaporanBaru
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
            $tahun = (int) ($request->get('tahun', date('Y')));
            $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;

            $query = DB::table('tr_peminjaman as peminjaman')
                ->join('mst_karyawan as guru', 'peminjaman.NIP_KARYAWAN', '=', 'guru.NIP_KARYAWAN')
                ->join('cp_koleksi as copy', 'peminjaman.ID_CP_KOLEKSI', '=', 'copy.ID_CP_KOLEKSI')
                ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
                ->where('guru.IS_DELETE', 0)
                ->whereRaw('LOWER(guru.JABATAN_FUNGSIONAL) = ?', ['guru'])
                ->where('buku.IS_DELETE', 0)
                ->where(function ($statusQuery) {
                    $statusQuery->whereNull('peminjaman.STATUS_PEMINJAMAN')
                        ->orWhere('peminjaman.STATUS_PEMINJAMAN', '!=', 'Dihapus');
                })
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
                    'guru.NIP_KARYAWAN as nip_karyawan',
                    'guru.NAMA_KARYAWAN as nama_guru',
                    'guru.JABATAN_FUNGSIONAL as jabatan_fungsional',
                    'buku.ISBN',
                    'buku.JUDUL_KOLEKSI as judul_koleksi',
                    'buku.PENGARANG as pengarang',
                    'buku.NO_RAK_BUKU as no_rak_buku'
                )
                ->orderBy('peminjaman.TGL_PINJAM', 'desc')
                ->get()
                ->values();

            $periodeLabel = $bulan !== null
                ? Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y')
                : 'Tahun ' . $tahun;

            return response()->json([
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
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function inventarisasiBukuBaru(Request $request)
    {
        try {
            $tahun = (int) ($request->get('tahun', date('Y')));
            $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;

            $query = DB::table('mst_koleksi_buku as buku')
                ->join('ref_koleksi as kategori', 'buku.ID_REF_KOLEKSI', '=', 'kategori.ID_REF_KOLEKSI')
                ->where('buku.IS_DELETE', 0)
                ->where('buku.ID_REF_KOLEKSI', '!=', 4)
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
                ->whereNotNull('buku.JUMLAH_EKSEMPLAR')
                ->where('buku.JUMLAH_EKSEMPLAR', '>', 0)
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
                    'buku.JUMLAH_EKSEMPLAR as jumlah_ekslempar',
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

            return response()->json([
                'filter' => [
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'periode_label' => $periodeLabel,
                ],
                'summary' => [
                    'total_buku_baru' => $books->count(),
                    'total_eksemplar' => (int) $books->sum('jumlah_ekslempar'),
                    'total_kategori' => $books->pluck('kategori')->unique()->count(),
                ],
                'data' => $books,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function distribusiKunjunganHari(Request $request)
    {
        try {
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

            return response()->json([
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
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function distribusiKunjunganKelas(Request $request)
    {
        try {
            $tahun = (int) ($request->get('tahun', date('Y')));
            $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;

            $baseQuery = DB::table('tr_kunjungan_perpus as kunjungan')
                ->join('mst_siswa as siswa', 'kunjungan.id_siswa_tetap', '=', 'siswa.id_siswa_tetap')
                ->where('siswa.is_delete', 0)
                ->whereNotNull('siswa.tahun_lulus')
                ->whereYear('kunjungan.start_kunjungan', $tahun);

            if ($bulan !== null) {
                $baseQuery->whereMonth('kunjungan.start_kunjungan', $bulan);
            }

            $kelasExpression = "
                CASE
                    WHEN UPPER(TRIM(siswa.tahun_lulus)) IN ('X', 'XI', 'XII') THEN UPPER(TRIM(siswa.tahun_lulus))
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

            return response()->json([
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
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function statistikPeminjamanBulanan(Request $request)
    {
        try {
            $tahun = (int) ($request->get('tahun', date('Y')));
            $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;

            if ($bulan !== null && ($bulan < 1 || $bulan > 12)) {
                return response()->json([
                    'message' => 'Bulan laporan tidak valid.',
                ], 422);
            }

            $query = DB::table('tr_peminjaman')
                ->whereNotNull('TGL_PINJAM')
                ->where(function ($statusQuery) {
                    $statusQuery->whereNull('STATUS_PEMINJAMAN')
                        ->orWhere('STATUS_PEMINJAMAN', '!=', 'Dihapus');
                })
                ->whereYear('TGL_PINJAM', $tahun);

            if ($bulan !== null) {
                $query->whereMonth('TGL_PINJAM', $bulan);
            }

            $rowsByMonth = (clone $query)
                ->selectRaw('MONTH(TGL_PINJAM) as nomor_bulan')
                ->selectRaw('COUNT(ID_PEMINJAMAN) as jumlah_peminjaman')
                ->groupBy('nomor_bulan')
                ->orderBy('nomor_bulan')
                ->get()
                ->keyBy('nomor_bulan');

            $monthNumbers = $bulan !== null ? collect([$bulan]) : collect(range(1, 12));

            $rows = $monthNumbers
                ->map(function ($item) use ($tahun) {
                    $tanggal = Carbon::create($tahun, (int) $item, 1);

                    return [
                        'nomor_bulan' => (int) $item,
                        'nama_bulan' => $tanggal->locale('id')->translatedFormat('F'),
                        'jumlah_peminjaman' => 0,
                    ];
                })
                ->map(function ($item) use ($rowsByMonth) {
                    $match = $rowsByMonth->get($item['nomor_bulan']);
                    $item['jumlah_peminjaman'] = (int) ($match->jumlah_peminjaman ?? 0);

                    return $item;
                })
                ->values();

            $totalPeminjaman = (clone $query)->count();

            if ($bulan !== null) {
                $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;
                $rowsByDay = (clone $query)
                    ->selectRaw('DAY(TGL_PINJAM) as tanggal')
                    ->selectRaw('COUNT(ID_PEMINJAMAN) as jumlah_peminjaman')
                    ->groupBy('tanggal')
                    ->orderBy('tanggal')
                    ->get()
                    ->keyBy('tanggal');

                $trend = collect(range(1, $daysInMonth))
                    ->map(function ($day) use ($tahun, $bulan, $rowsByDay) {
                        $tanggal = Carbon::create($tahun, $bulan, (int) $day);
                        $match = $rowsByDay->get($day);

                        return [
                            'label' => $tanggal->locale('id')->translatedFormat('d M'),
                            'nomor_hari' => (int) $day,
                            'jumlah_peminjaman' => (int) ($match->jumlah_peminjaman ?? 0),
                        ];
                    })
                    ->values();
            } else {
                $trend = $rows
                    ->map(function ($item) {
                        return [
                            'label' => $item['nama_bulan'],
                            'nomor_bulan' => $item['nomor_bulan'],
                            'jumlah_peminjaman' => $item['jumlah_peminjaman'],
                        ];
                    })
                    ->values();
            }

            $periodeLabel = $bulan !== null
                ? Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y')
                : 'Tahun ' . $tahun;

            return response()->json([
                'filter' => [
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'periode_label' => $periodeLabel,
                ],
                'summary' => [
                    'total_peminjaman' => $totalPeminjaman,
                    'jumlah_bulan_aktif' => $rows->where('jumlah_peminjaman', '>', 0)->count(),
                ],
                'data' => $rows,
                'trend' => $trend,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLaporan(Request $request)
        {
            try {
                $query = DB::table('mst_koleksi_buku')
                    ->where('ID_REF_KOLEKSI', 4) 
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
            ->select(
                'ms.nama_siswa_tetap',
                'ms.nisn_siswa',
                DB::raw('COUNT(tp.ID_PEMINJAMAN) as peminjaman_count')
            )
            ->groupBy('ms.id_siswa_tetap', 'ms.nama_siswa_tetap', 'ms.nisn_siswa')
            ->orderBy('peminjaman_count', 'desc')
            ->take(10)
            ->get();

        return response()->json($siswaTerajin);
    }

    public function exportPdfSiswaTerajin()
    {
        $siswaTerajin = DB::table('tr_peminjaman as tp')
            ->join('mst_siswa as ms', 'tp.ID_SISWA_TETAP', '=', 'ms.id_siswa_tetap')
            ->select(
                'ms.nama_siswa_tetap',
                'ms.nisn_siswa',
                DB::raw('COUNT(tp.ID_PEMINJAMAN) as peminjaman_count')
            )
            ->groupBy('ms.id_siswa_tetap', 'ms.nama_siswa_tetap', 'ms.nisn_siswa')
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
}
