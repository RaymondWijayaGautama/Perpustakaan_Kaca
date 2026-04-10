<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function getStats()
    {
        // 1. Menghitung jumlah total fisik buku (Kecuali kategori Laporan/PKL ID 4)
        $totalBuku = DB::table('mst_koleksi_buku')
            ->where('is_delete', 0)
            ->where('id_ref_koleksi', '!=', 4) 
            ->sum('jumlah_ekslempar');

        // 2. Menghitung total anggota (Siswa + Karyawan)
        $jumlahSiswa = DB::table('mst_siswa')->where('is_delete', 0)->count();
        $jumlahKaryawan = DB::table('mst_karyawan')->where('is_delete', 0)->count();

        // 3. Menghitung total Laporan PKL (id_ref_koleksi = 4)
        $totalLaporan = DB::table('mst_koleksi_buku')
            ->where('is_delete', 0)
            ->where('id_ref_koleksi', 4)
            ->count();

        return response()->json([
            'total_buku' => $totalBuku,
            'total_siswa' => $jumlahSiswa + $jumlahKaryawan,
            'total_laporan' => $totalLaporan
        ]);
    }

    public function getAnggota(Request $request)
    {
        try {
            // Default 10 data per halaman
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            $siswa = DB::table('mst_siswa')
                ->where('is_delete', 0)
                ->select(
                    'nisn_siswa as identitas', 
                    'nama_siswa_tetap as nama', 
                    'gender_siswa as gender', 
                    DB::raw("'Siswa' as role")
                );

            $results = DB::table('mst_karyawan')
                ->where('is_delete', 0)
                ->select(
                    'nip_karyawan as identitas', 
                    'nama_karyawan as nama', 
                    'gender_karyawan as gender', 
                    DB::raw("'Karyawan' as role")
                )
                ->union($siswa)
                ->get(); 

            $sorted = $results->sortBy('nama')->values();
            $currentPageItems = $sorted->forPage($page, $perPage)->values();

            $paginatedData = new LengthAwarePaginator(
                $currentPageItems,
                $sorted->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return response()->json($paginatedData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBuku(Request $request)
    {
        $query = DB::table('mst_koleksi_buku')
            ->join('ref_koleksi', 'mst_koleksi_buku.id_ref_koleksi', '=', 'ref_koleksi.id_ref_koleksi')
            ->where('mst_koleksi_buku.is_delete', 0)
            ->where('mst_koleksi_buku.id_ref_koleksi', '!=', 4) 
            ->select(
                'mst_koleksi_buku.ISBN',
                'mst_koleksi_buku.judul_koleksi',
                'mst_koleksi_buku.pengarang',
                'mst_koleksi_buku.tahun',
                'mst_koleksi_buku.jumlah_ekslempar',
                'mst_koleksi_buku.keterangan_buku', 
                'ref_koleksi.deskripsi as kategori'
            );

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('mst_koleksi_buku.judul_koleksi', 'like', '%' . $request->search . '%')
                  ->orWhere('mst_koleksi_buku.pengarang', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('kategori')) {
            $query->where('ref_koleksi.deskripsi', $request->kategori);
        }

        $sortBy = $request->get('sort_by', 'judul_koleksi');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Menggunakan default 10 data per halaman
        return response()->json($query->paginate($request->get('per_page', 10)));
    }
}