<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanPklController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Tangkap parameter dari React
            $judul = $request->query('judul');
            $penulis = $request->query('penulis');
            $tahun = $request->query('tahun'); // Tetap dipertahankan untuk kelengkapan
            $perPage = $request->query('per_page', 10);

            $query = DB::table('mst_koleksi_laporan')
                ->join('cp_koleksi', 'mst_koleksi_laporan.id_mst_laporan', '=', 'cp_koleksi.id_mst_laporan')
                ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
                ->leftJoin('pkl_siswa', 'mst_koleksi_laporan.id_pkl_siswa', '=', 'pkl_siswa.id_pkl_siswa')
                ->leftJoin('mst_siswa', 'pkl_siswa.id_siswa_tetap', '=', 'mst_siswa.id_siswa_tetap')
                ->leftJoin('mitra_pkl', 'pkl_siswa.id_mitra_pkl', '=', 'mitra_pkl.id_mitra_pkl')
                ->select(
                    'mst_koleksi_laporan.id_mst_laporan',
                    'cp_koleksi.ISBN as isbn',
                    'mst_koleksi_buku.judul_koleksi',
                    'mst_koleksi_buku.tahun',
                    DB::raw("COALESCE(mst_siswa.nama_siswa_tetap, mst_koleksi_buku.pengarang) as nama_siswa_tetap"),
                    'mst_siswa.nisn_siswa',
                    'mitra_pkl.nama_mitra_pkl as mitra'
                );

            // Filter Spesifik: Judul
            if (!empty($judul)) {
                $query->where('mst_koleksi_buku.judul_koleksi', 'LIKE', "%{$judul}%");
            }

            // Filter Spesifik: Penulis (Mencari di data Siswa ATAU Pengarang Buku)
            if (!empty($penulis)) {
                $query->where(function($q) use ($penulis) {
                    $q->where('mst_siswa.nama_siswa_tetap', 'LIKE', "%{$penulis}%")
                      ->orWhere('mst_koleksi_buku.pengarang', 'LIKE', "%{$penulis}%");
                });
            }

            // Filter Spesifik: Tahun
            if (!empty($tahun)) {
                $query->where('mst_koleksi_buku.tahun', '=', $tahun);
            }

            $results = $query->orderBy('mst_koleksi_buku.tahun', 'desc')->paginate($perPage);

            return response()->json($results);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memuat data laporan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLaporanPkl(Request $request)
    {
        try {
            $judul = $request->query('judul');
            $penulis = $request->query('penulis');
            $sortBy = $request->query('sort_by', 'judul_koleksi');
            $sortOrder = $request->query('sort_order', 'desc');
            $perPage = $request->query('per_page', 8);

            $query = DB::table('mst_koleksi_buku')
                ->leftJoin('ref_koleksi', 'mst_koleksi_buku.id_ref_koleksi', '=', 'ref_koleksi.id_ref_koleksi')
                ->select('mst_koleksi_buku.*', 'ref_koleksi.deskripsi as kategori')
                ->where('mst_koleksi_buku.is_delete', 0)
                ->where('mst_koleksi_buku.id_ref_koleksi', 4);

            if (!empty($judul)) {
                $query->where('mst_koleksi_buku.judul_koleksi', 'LIKE', "%{$judul}%");
            }

            if (!empty($penulis)) {
                $query->where('mst_koleksi_buku.pengarang', 'LIKE', "%{$penulis}%");
            }

            $allowedSort = ['judul_koleksi', 'pengarang', 'tahun'];
            $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'judul_koleksi';

            // Menambahkan prefix tabel pada orderBy agar tidak ambigu
            $books = $query->orderBy('mst_koleksi_buku.' . $sortBy, $sortOrder)->paginate($perPage);

            return response()->json($books);

        } catch (\Exception $e) {
            // CCTV Error: Jika gagal, Laravel akan mengirimkan detail errornya ke React
            return response()->json([
                'message' => 'Gagal mengambil data Laporan',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}