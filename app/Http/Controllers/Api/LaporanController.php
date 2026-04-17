<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // WAJIB ADA untuk Query Builder
use Illuminate\Support\Facades\Validator; // WAJIB ADA untuk fungsi store
use App\Models\MstKoleksiLaporan;
use App\Models\CpKoleksi; // Pastikan model ini ada jika digunakan di destroy
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\Siswa;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function laporanPeminjamanGuru(Request $request)
    {
        try {
            $tahun = (int) ($request->get('tahun', date('Y')));
            $bulan = $request->filled('bulan') ? (int) $request->get('bulan') : null;

            $query = DB::table('tr_peminjaman as peminjaman')
                ->join('mst_karyawan as guru', 'peminjaman.nip_karyawan', '=', 'guru.nip_karyawan')
                ->join('cp_koleksi as copy', 'peminjaman.id_cp_koleksi', '=', 'copy.id_cp_koleksi')
                ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
                ->where('guru.is_delete', 0)
                ->whereRaw('LOWER(guru.jabatan_fungsional) = ?', ['guru'])
                ->where('buku.is_delete', 0)
                ->where('peminjaman.status_peminjaman', '!=', 'Dihapus')
                ->whereYear('peminjaman.tgl_peminjaman', $tahun);

            if ($bulan !== null) {
                $query->whereMonth('peminjaman.tgl_peminjaman', $bulan);
            }

            $data = (clone $query)
                ->select(
                    'peminjaman.id_peminjaman',
                    'peminjaman.tgl_peminjaman',
                    'peminjaman.tgl_harus_kembali',
                    'peminjaman.tgl_kembali',
                    'peminjaman.status_peminjaman',
                    'guru.nip_karyawan',
                    'guru.nama_karyawan as nama_guru',
                    'guru.jabatan_fungsional',
                    'buku.ISBN',
                    'buku.judul_koleksi',
                    'buku.pengarang',
                    'buku.no_rak_buku'
                )
                ->orderBy('peminjaman.tgl_peminjaman', 'desc')
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
                    'buku.jumlah_ekslempar',
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
                ->whereYear('tgl_peminjaman', $tahun);

            if ($bulan !== null) {
                $query->whereMonth('tgl_peminjaman', $bulan);
            }

            $rows = (clone $query)
                ->selectRaw('MONTH(tgl_peminjaman) as nomor_bulan')
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
            // Pastikan menggunakan Facade DB (sudah di-import di atas)
            $query = DB::table('mst_koleksi_buku')
                ->where('id_ref_koleksi', 4) // Angka 4 = Kategori PKL
                ->where('is_delete', 0)
                ->select(
                    'judul_koleksi', 
                    'pengarang as nama_siswa_tetap', 
                    'tahun'
                );

            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            if ($request->filled('penulis')) {
                $query->where('pengarang', 'like', '%' . $request->penulis . '%');
            }

            // Return data pagination (5 data per halaman)
            return response()->json($query->paginate(5));

        } catch (\Exception $e) {
            // Mengirim pesan error asli ke React untuk mempermudah debugging
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        // Validator sekarang sudah dikenali karena import di atas
        $validator = Validator::make($request->all(), [
            'judul_laporan' => 'required|string|max:255',
            'penulis_laporan' => 'required|string|max:100',
            'tahun_laporan' => 'required|digits:4',
            'file_laporan' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'pesan' => $validator->errors()], 400);
        }

        try {
            $file = $request->file('file_laporan');
            $namaFile = time() . '_' . $file->getClientOriginalName(); 
            $file->storeAs('public/laporan', $namaFile);

            $laporan = MstKoleksiLaporan::create([
                'id_mst_laporan' => $request->id_mst_laporan,
                'is_delete' => 0
            ]);

            return response()->json(['status' => 'success', 'data' => $laporan], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'pesan' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $laporan = MstKoleksiLaporan::find($id);

        if (!$laporan) {
            return response()->json(['status' => 'error', 'pesan' => 'Data laporan tidak ditemukan!'], 404);
        }
        $validator = Validator::make($request->all(), [
            'judul_laporan' => 'required|string|max:255',
            'penulis_laporan' => 'required|string|max:100',
            'tahun_laporan' => 'required|digits:4',
            'file_laporan' => 'nullable|file|mimes:pdf,doc,docx|max:10240', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'pesan' => $validator->errors()], 400);
        }
        if ($request->hasFile('file_laporan')) {
            if (Storage::exists('public/laporan/' . $laporan->file_path)) {
                Storage::delete('public/laporan/' . $laporan->file_path);
            }
            $file = $request->file('file_laporan');
            $namaFile = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName()); 
            $file->storeAs('public/laporan', $namaFile);

            $laporan->file_path = $namaFile;
        }

        $laporan->judul_laporan = $request->judul_laporan;
        $laporan->penulis_laporan = $request->penulis_laporan;
        $laporan->tahun_laporan = $request->tahun_laporan;
        $laporan->save(); 

        return response()->json([
            'status' => 'success',
            'pesan' => 'Data laporan berhasil diubah!',
            'data' => $laporan
        ], 200);
    }

    public function destroy($id)
    {
        try {
            $laporan = MstKoleksiLaporan::find($id);

            if (!$laporan) {
                return response()->json(['status' => 'error', 'pesan' => 'Data tidak ditemukan!'], 404);
            }

            // Cek relasi ke tabel cp_koleksi
            $dipakai = DB::table('cp_koleksi')->where('id_mst_laporan', $id)->exists();
            if ($dipakai) {
                return response()->json(['status' => 'error', 'pesan' => 'Gagal! Laporan sedang terhubung dengan buku fisik.'], 400); 
            }

            $laporan->is_delete = 1;
            $laporan->save();

            return response()->json(['status' => 'success', 'pesan' => 'Berhasil dihapus!'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'pesan' => $e->getMessage()], 500);
        }
    }

    public function siswaTerajin()
    {
        
        $siswaTerajin = Siswa::withCount('peminjaman')
            ->orderBy('peminjaman_count', 'desc') 
            ->take(10) 
            ->get();

        return view('laporan.siswa_terajin', compact('siswaTerajin'));
    }

    public function exportPdfSiswaTerajin()
    {
    
    $siswaTerajin = Siswa::withCount('peminjaman')
        ->orderBy('peminjaman_count', 'desc')
        ->take(10)
        ->get();

    
    $data = [
        'siswaTerajin' => $siswaTerajin,
        'periode'      => 'Maret - April 2026',
        'tahun_ajaran' => '2025/2026', 
        'juara1'       => optional($siswaTerajin->get(0)),
        'juara2'       => optional($siswaTerajin->get(1)),
        'juara3'       => optional($siswaTerajin->get(2)),
    ];

    // Lempar sebagai HTML murni
    return view('laporan.pdf_siswa_terajin', $data);
    }

    public function kunjunganBulanan()
    {
        $tahun = date('Y');
        $laporanKunjungan = \App\Models\Kunjungan::select(
                DB::raw('MONTHNAME(start_kunjungan) as bulan'),
                DB::raw('MONTH(start_kunjungan) as urutan_bulan'),
                DB::raw('COUNT(*) as total_kunjungan')
            )
            ->groupBy('bulan', 'urutan_bulan')
            ->orderBy('urutan_bulan', 'asc')
            ->get();

        return view('laporan.kunjungan_bulanan', compact('laporanKunjungan'));
    }
    
    public function exportPdfKunjungan()
{
    $laporanKunjungan = \App\Models\Kunjungan::select(
            DB::raw('MONTHNAME(start_kunjungan) as bulan'),
            DB::raw('MONTH(start_kunjungan) as urutan_bulan'),
            DB::raw('COUNT(*) as total_kunjungan')
        )
        ->groupBy('bulan', 'urutan_bulan')
        ->orderBy('urutan_bulan', 'asc')
        ->get();

    $pdf = Pdf::loadView('laporan.kunjungan_bulanan_pdf', compact('laporanKunjungan'));
    
    // Set kertas A4
    $pdf->setPaper('a4', 'portrait');

    return $pdf->download('Laporan_Jumlah_Kunjungan_Perpus.pdf');
}

    public function bukuTerpopuler()
    {
        $tahun = date('Y');

        $laporanBuku = DB::table('tr_peminjaman as tp')
            ->join('cp_koleksi as ck', 'tp.id_cp_koleksi', '=', 'ck.id_cp_koleksi')
            ->join('mst_koleksi_buku as mkb', 'ck.ISBN', '=', 'mkb.ISBN')
            ->select(
                'mkb.judul_koleksi',
                'mkb.ISBN',
                'mkb.pengarang',
                DB::raw('COUNT(tp.id_peminjaman) as total_dipinjam')
            )
            ->whereYear('tp.tgl_peminjaman', $tahun)
            ->groupBy('mkb.ISBN', 'mkb.judul_koleksi', 'mkb.pengarang')
            ->orderBy('total_dipinjam', 'desc')
            ->take(20) 
            ->get();

        return view('laporan.buku_terpopuler', compact('laporanBuku', 'tahun'));
    }

    public function exportPdfBukuTerpopuler()
    {
        $tahun = date('Y');

        $laporanBuku = DB::table('tr_peminjaman as tp')
            ->join('cp_koleksi as ck', 'tp.id_cp_koleksi', '=', 'ck.id_cp_koleksi')
            ->join('mst_koleksi_buku as mkb', 'ck.ISBN', '=', 'mkb.ISBN')
            ->select(
                'mkb.judul_koleksi',
                'mkb.ISBN',
                'mkb.pengarang',
                DB::raw('COUNT(tp.id_peminjaman) as total_dipinjam')
            )
            ->whereYear('tp.tgl_peminjaman', $tahun)
            ->groupBy('mkb.ISBN', 'mkb.judul_koleksi', 'mkb.pengarang')
            ->orderBy('total_dipinjam', 'desc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.buku_terpopuler_pdf', compact('laporanBuku', 'tahun'));
        return $pdf->download('Laporan_Buku_Terpopuler_'.$tahun.'.pdf');
    }
    // Fungsionalitas 56: Laporan Kategori Buku Paling Sering Dipinjam
    public function kategoriPopuler()
    {
        $tahun = date('Y');

        $laporanKategori = DB::table('tr_peminjaman as tp')
            ->join('cp_koleksi as ck', 'tp.id_cp_koleksi', '=', 'ck.id_cp_koleksi')
            ->join('mst_koleksi_buku as mkb', 'ck.ISBN', '=', 'mkb.ISBN')
            ->join('ref_koleksi as rk', 'mkb.id_ref_koleksi', '=', 'rk.id_ref_koleksi')
            ->select('rk.deskripsi', DB::raw('COUNT(tp.id_peminjaman) as total_dipinjam'))
            ->whereYear('tp.tgl_peminjaman', $tahun)
            ->groupBy('rk.id_ref_koleksi', 'rk.deskripsi')
            ->orderBy('total_dipinjam', 'desc')
            ->get();

        return view('laporan.kategori_populer', compact('laporanKategori', 'tahun'));
    }

    public function exportPdfKategori()
    {
        $tahun = date('Y');

        $laporanKategori = DB::table('tr_peminjaman as tp')
            ->join('cp_koleksi as ck', 'tp.id_cp_koleksi', '=', 'ck.id_cp_koleksi')
            ->join('mst_koleksi_buku as mkb', 'ck.ISBN', '=', 'mkb.ISBN')
            ->join('ref_koleksi as rk', 'mkb.id_ref_koleksi', '=', 'rk.id_ref_koleksi')
            ->select('rk.deskripsi', DB::raw('COUNT(tp.id_peminjaman) as total_dipinjam'))
            ->whereYear('tp.tgl_peminjaman', $tahun)
            ->groupBy('rk.id_ref_koleksi', 'rk.deskripsi')
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
