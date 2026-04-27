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
                ->join('mst_karyawan as guru', 'peminjaman.NIP_KARYAWAN', '=', 'guru.nip_karyawan')
                ->join('cp_koleksi as copy', 'peminjaman.ID_CP_KOLEKSI', '=', 'copy.id_cp_koleksi')
                ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
                ->where('guru.is_delete', 0)
                ->whereRaw('LOWER(guru.jabatan_fungsional) = ?', ['guru'])
                ->where('buku.is_delete', 0)
                ->where('peminjaman.STATUS_PEMINJAMAN', '!=', 'Dihapus')
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
                ->join('ref_koleksi as kategori', 'buku.id_ref_koleksi', '=', 'kategori.id_ref_koleksi')
                ->where('buku.is_delete', 0)
                ->where('buku.id_ref_koleksi', '!=', 4)
                ->whereNotNull('buku.ISBN')
                ->whereNotNull('buku.judul_koleksi')
                ->where('buku.judul_koleksi', '!=', '')
                ->whereNotNull('buku.pengarang')
                ->where('buku.pengarang', '!=', '')
                ->whereNotNull('buku.penerbit')
                ->where('buku.penerbit', '!=', '')
                ->whereNotNull('buku.tgl_masuk_koleksi')
                ->whereNotNull('buku.id_ref_koleksi')
                ->whereNotNull('buku.no_rak_buku')
                ->where('buku.no_rak_buku', '!=', '')
                ->whereYear('buku.tgl_masuk_koleksi', $tahun);

            if ($bulan !== null) {
                $query->whereMonth('buku.tgl_masuk_koleksi', $bulan);
            }

            $books = (clone $query)
                ->select(
                    'buku.ISBN',
                    'buku.judul_koleksi',
                    'buku.pengarang',
                    'buku.penerbit',
                    'buku.tahun',
                    'buku.tgl_masuk_koleksi',
                    'buku.no_rak_buku',
                    'buku.jumlah_ekslempar', // Note: Pastikan di database memang jumlah_ekslempar (bukan eksemplar)
                    'kategori.deskripsi as kategori'
                )
                ->distinct()
                ->orderBy('buku.tgl_masuk_koleksi', 'desc')
                ->orderBy('buku.judul_koleksi')
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

            $query = DB::table('tr_peminjaman')
                ->whereYear('TGL_PINJAM', $tahun);

            if ($bulan !== null) {
                $query->whereMonth('TGL_PINJAM', $bulan);
            }

            $rows = (clone $query)
                ->selectRaw('MONTH(TGL_PINJAM) as nomor_bulan')
                ->selectRaw('COUNT(*) as jumlah_peminjaman')
                ->groupBy('nomor_bulan')
                ->orderBy('nomor_bulan')
                ->get()
                ->map(function ($item) use ($tahun) {
                    $tanggal = Carbon::create($tahun, $item->nomor_bulan, 1);

                    return [
                        'nomor_bulan' => (int) $item->nomor_bulan,
                        'nama_bulan' => $tanggal->locale('id')->translatedFormat('F'),
                        'jumlah_peminjaman' => (int) $item->jumlah_peminjaman,
                    ];
                })
                ->values();

            $totalPeminjaman = (clone $query)->count();

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
                    'jumlah_bulan_aktif' => $rows->count(),
                ],
                'data' => $rows,
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
            $query = DB::table('pkl_siswa')
                ->join('mst_siswa', 'pkl_siswa.ID_SISWA_TETAP', '=', 'mst_siswa.ID_SISWA_TETAP')
                ->select(
                    'pkl_siswa.ID_PKL_SISWA as ISBN', 
                    'pkl_siswa.JUDUL_LAPORAN_PKL as judul_koleksi', 
                    'mst_siswa.NAMA_SISWA_TETAP as nama_siswa_tetap', 
                    'mst_siswa.TAHUN_LULUS as tahun'
                );

            if ($request->filled('judul')) {
                $query->where('pkl_siswa.JUDUL_LAPORAN_PKL', 'like', '%' . $request->judul . '%');
            }

            if ($request->filled('penulis')) {
                $query->where('mst_siswa.NAMA_SISWA_TETAP', 'like', '%' . $request->penulis . '%');
            }

            return response()->json($query->paginate(5));

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