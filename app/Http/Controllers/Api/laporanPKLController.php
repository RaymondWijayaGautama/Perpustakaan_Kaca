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
            $judul = $request->query('judul');
            $penulis = $request->query('penulis');
            $tahun = $request->query('tahun');
            $perPage = $request->query('per_page', 10);
            
            // Ambil data dasar buku dulu (hanya buku dengan id_ref_koleksi = 4 alias Laporan)
            $query = DB::table('mst_koleksi_buku')
                ->where('mst_koleksi_buku.id_ref_koleksi', 4)
                ->where('mst_koleksi_buku.is_delete', 0);

            // Filter
            if (!empty($judul)) {
                $query->where('mst_koleksi_buku.judul_koleksi', 'LIKE', "%{$judul}%");
            }
            if (!empty($tahun)) {
                $query->where('mst_koleksi_buku.tahun', $tahun);
            }
            if (!empty($penulis)) {
                $query->where('mst_koleksi_buku.pengarang', 'LIKE', "%{$penulis}%");
            }

            // Sort & Paginate (Sangat aman)
            $results = $query->orderBy('mst_koleksi_buku.tahun', 'desc')->paginate($perPage);

            // Karena data siswa ada di tabel terpisah, kita map secara manual
            // Ini untuk menghindari error Join yang kompleks di MySQL
            $results->getCollection()->transform(function ($buku) {
                
                // Cari data fisik buku
                $fisik = DB::table('cp_koleksi')
                    ->where('ISBN', $buku->ISBN)
                    ->first();
                
                $nama_siswa = null;
                $nisn_siswa = null;

                if ($fisik) {
                    // Cari data laporan terkait fisik ini
                    $laporan = DB::table('mst_koleksi_laporan')
                        ->where('id_mst_laporan', $fisik->id_mst_laporan)
                        ->first();

                    if ($laporan && $laporan->id_pkl_siswa) {
                        // Cari data siswanya
                        $pkl = DB::table('pkl_siswa')
                            ->join('mst_siswa', 'pkl_siswa.id_siswa_tetap', '=', 'mst_siswa.id_siswa_tetap')
                            ->where('pkl_siswa.id_pkl_siswa', $laporan->id_pkl_siswa)
                            ->select('mst_siswa.nama_siswa_tetap', 'mst_siswa.nisn_siswa')
                            ->first();

                        if ($pkl) {
                            $nama_siswa = $pkl->nama_siswa_tetap;
                            $nisn_siswa = $pkl->nisn_siswa;
                        }
                    }
                }

                return [
                    'id_cp_koleksi' => $fisik ? $fisik->id_cp_koleksi : null,
                    'ISBN' => $buku->ISBN,
                    'judul_koleksi' => $buku->judul_koleksi,
                    'tahun' => $buku->tahun,
                    'no_rak_buku' => $buku->no_rak_buku,
                    'kategori' => 'Laporan PKL',
                    'status_buku' => $fisik ? $fisik->status_buku : 'Tersedia',
                    // Atribut mutlak untuk React:
                    'nama_siswa_tetap' => $nama_siswa ? $nama_siswa : $buku->pengarang,
                    'nisn_siswa' => $nisn_siswa,
                ];
            });

            return response()->json($results);

        } catch (\Exception $e) {
            // Melempar error ke layar agar kita bisa membaca pesannya
            return response()->json([
                'message' => 'Gagal, cek tab Preview di Network',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}