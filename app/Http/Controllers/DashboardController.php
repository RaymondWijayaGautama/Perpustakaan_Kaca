<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $allowedSortFields = [
            'judul_koleksi' => 'mst_koleksi_buku.judul_koleksi',
            'pengarang' => 'mst_koleksi_buku.pengarang',
            'tahun' => 'mst_koleksi_buku.tahun',
            'kategori' => 'ref_koleksi.deskripsi',
        ];

        $search = trim((string) $request->get('search', ''));

        $query = DB::table('mst_koleksi_buku')
            ->join('ref_koleksi', 'mst_koleksi_buku.id_ref_koleksi', '=', 'ref_koleksi.id_ref_koleksi')
            ->where('mst_koleksi_buku.is_delete', 0)
            ->where('mst_koleksi_buku.id_ref_koleksi', '!=', 4) 
            ->select(
                'mst_koleksi_buku.ISBN',
                'mst_koleksi_buku.judul_koleksi',
                'mst_koleksi_buku.pengarang',
                'mst_koleksi_buku.penerbit',
                'mst_koleksi_buku.tahun',
                'mst_koleksi_buku.jumlah_ekslempar',
                'mst_koleksi_buku.no_rak_buku',
                'mst_koleksi_buku.keterangan_buku', 
                'mst_koleksi_buku.id_ref_koleksi',
                'ref_koleksi.deskripsi as kategori'
            );

        if ($search !== '') {
            $keywords = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->where(function ($inner) use ($keyword) {
                        $inner->where('mst_koleksi_buku.judul_koleksi', 'like', '%' . $keyword . '%')
                            ->orWhere('mst_koleksi_buku.pengarang', 'like', '%' . $keyword . '%')
                            ->orWhere('mst_koleksi_buku.ISBN', 'like', '%' . $keyword . '%');
                    });
                }
            });

            $escapedSearch = addcslashes($search, '%_');
            $prefixSearch = $escapedSearch . '%';
            $containsSearch = '%' . $escapedSearch . '%';

            $query->selectRaw(
                "(
                    CASE
                        WHEN mst_koleksi_buku.ISBN = ? THEN 120
                        WHEN mst_koleksi_buku.judul_koleksi = ? THEN 110
                        WHEN mst_koleksi_buku.pengarang = ? THEN 100
                        WHEN mst_koleksi_buku.ISBN LIKE ? THEN 90
                        WHEN mst_koleksi_buku.judul_koleksi LIKE ? THEN 80
                        WHEN mst_koleksi_buku.pengarang LIKE ? THEN 70
                        WHEN mst_koleksi_buku.judul_koleksi LIKE ? THEN 60
                        WHEN mst_koleksi_buku.pengarang LIKE ? THEN 50
                        WHEN mst_koleksi_buku.ISBN LIKE ? THEN 40
                        ELSE 0
                    END
                ) as relevance_score",
                [
                    $search,
                    $search,
                    $search,
                    $prefixSearch,
                    $prefixSearch,
                    $prefixSearch,
                    $containsSearch,
                    $containsSearch,
                    $containsSearch,
                ]
            );
        }

        if ($request->filled('kategori')) {
            $query->where(function ($q) use ($request) {
                $q->where('ref_koleksi.deskripsi', $request->kategori)
                  ->orWhere('mst_koleksi_buku.id_ref_koleksi', $request->kategori);
            });
        }

        $sortBy = $request->get('sort_by', 'judul_koleksi');
        $sortOrder = strtolower((string) $request->get('sort_order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $sortColumn = $allowedSortFields[$sortBy] ?? $allowedSortFields['judul_koleksi'];

        if ($search !== '') {
            $query->orderByDesc('relevance_score');
        }

        $query->orderBy($sortColumn, $sortOrder);

        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min($perPage, 50));

        return response()->json($query->paginate($perPage));
    }

    public function getKategoriBuku()
    {
        $kategori = DB::table('ref_koleksi')
            ->where('is_delete', 0)
            ->where('id_ref_koleksi', '!=', 4)
            ->orderBy('deskripsi')
            ->get(['id_ref_koleksi', 'deskripsi']);

        return response()->json($kategori);
    }

    public function updateBuku(Request $request, string $isbn)
    {
        $validator = validator($request->all(), [
            'editor_nip_karyawan' => ['required', 'string', 'max:20'],
            'judul_koleksi' => ['required', 'string', 'max:255'],
            'pengarang' => ['required', 'string', 'max:25'],
            'penerbit' => ['required', 'string', 'max:25'],
            'tahun' => ['required', 'digits:4'],
            'jumlah_ekslempar' => ['required', 'integer', 'min:0'],
            'no_rak_buku' => ['required', 'string', 'max:100'],
            'keterangan_buku' => ['nullable', 'string', 'max:255'],
            'id_ref_koleksi' => ['required', 'integer', Rule::exists('ref_koleksi', 'id_ref_koleksi')->where('is_delete', 0)],
        ], [
            'editor_nip_karyawan.required' => 'Identitas pustakawan wajib dikirim.',
            'id_ref_koleksi.exists' => 'Kategori buku tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pustakawan = DB::table('mst_karyawan')
            ->where('nip_karyawan', $request->editor_nip_karyawan)
            ->where('is_delete', 0)
            ->first();

        if (!$pustakawan || strtolower((string) $pustakawan->jabatan_fungsional) !== 'pustakawan') {
            return response()->json([
                'message' => 'Hanya pustakawan yang dapat mengubah koleksi buku.',
            ], 403);
        }

        $buku = DB::table('mst_koleksi_buku')
            ->where('ISBN', $isbn)
            ->where('is_delete', 0)
            ->first();

        if (!$buku) {
            return response()->json([
                'message' => 'Data buku tidak ditemukan.',
            ], 404);
        }

        DB::table('mst_koleksi_buku')
            ->where('ISBN', $isbn)
            ->update([
                'judul_koleksi' => $request->judul_koleksi,
                'pengarang' => $request->pengarang,
                'penerbit' => $request->penerbit,
                'tahun' => $request->tahun,
                'jumlah_ekslempar' => $request->jumlah_ekslempar,
                'no_rak_buku' => $request->no_rak_buku,
                'keterangan_buku' => $request->keterangan_buku ?: '',
                'id_ref_koleksi' => $request->id_ref_koleksi,
            ]);

        $updatedBook = DB::table('mst_koleksi_buku')
            ->join('ref_koleksi', 'mst_koleksi_buku.id_ref_koleksi', '=', 'ref_koleksi.id_ref_koleksi')
            ->where('mst_koleksi_buku.ISBN', $isbn)
            ->select(
                'mst_koleksi_buku.ISBN',
                'mst_koleksi_buku.judul_koleksi',
                'mst_koleksi_buku.pengarang',
                'mst_koleksi_buku.penerbit',
                'mst_koleksi_buku.tahun',
                'mst_koleksi_buku.jumlah_ekslempar',
                'mst_koleksi_buku.no_rak_buku',
                'mst_koleksi_buku.keterangan_buku',
                'mst_koleksi_buku.id_ref_koleksi',
                'ref_koleksi.deskripsi as kategori'
            )
            ->first();

        return response()->json([
            'message' => 'Koleksi buku berhasil diperbarui.',
            'data' => $updatedBook,
        ]);
    }

    public function destroyBuku(Request $request, string $isbn)
    {
        $validator = validator($request->all(), [
            'editor_nip_karyawan' => ['required', 'string', 'max:20'],
        ], [
            'editor_nip_karyawan.required' => 'Identitas pustakawan wajib dikirim.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pustakawan = DB::table('mst_karyawan')
            ->where('nip_karyawan', $request->editor_nip_karyawan)
            ->where('is_delete', 0)
            ->first();

        if (!$pustakawan || strtolower((string) $pustakawan->jabatan_fungsional) !== 'pustakawan') {
            return response()->json([
                'message' => 'Hanya pustakawan yang dapat menghapus koleksi buku.',
            ], 403);
        }

        $buku = DB::table('mst_koleksi_buku')
            ->where('ISBN', $isbn)
            ->where('is_delete', 0)
            ->first();

        if (!$buku) {
            return response()->json([
                'message' => 'Data buku tidak ditemukan.',
            ], 404);
        }

        $sedangDipinjam = DB::table('cp_koleksi')
            ->join('tr_peminjaman', 'cp_koleksi.id_cp_koleksi', '=', 'tr_peminjaman.id_cp_koleksi')
            ->where('cp_koleksi.ISBN', $isbn)
            ->whereNull('tr_peminjaman.tgl_kembali')
            ->exists();

        if ($sedangDipinjam) {
            return response()->json([
                'message' => 'Buku tidak bisa dihapus karena sedang dipinjam.',
            ], 409);
        }

        DB::table('mst_koleksi_buku')
            ->where('ISBN', $isbn)
            ->update([
                'is_delete' => 1,
            ]);

        DB::table('cp_koleksi')
            ->where('ISBN', $isbn)
            ->delete();

        return response()->json([
            'message' => 'Koleksi buku berhasil dihapus.',
        ]);
    }
}
