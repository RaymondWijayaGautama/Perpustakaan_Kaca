<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon; // Tambahan untuk pengolahan tanggal

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
        try {
            $judul = $request->query('judul');
            $penulis = $request->query('penulis');
            $kategori = $request->query('kategori');
            $sortBy = $request->query('sort_by', 'judul_koleksi');
            $sortOrder = $request->query('sort_order', 'asc');
            $perPage = $request->query('per_page', 8);

            $query = DB::table('mst_koleksi_buku')
                ->leftJoin('ref_koleksi', 'mst_koleksi_buku.id_ref_koleksi', '=', 'ref_koleksi.id_ref_koleksi')
                // KEMBALIKAN KE DESKRIPSI
                ->select('mst_koleksi_buku.*', 'ref_koleksi.deskripsi as kategori')
                ->where('mst_koleksi_buku.is_delete', 0)
                ->where('mst_koleksi_buku.id_ref_koleksi', '!=', 4);

            if (!empty($judul)) {
                $query->where('mst_koleksi_buku.judul_koleksi', 'LIKE', "%{$judul}%");
            }

            if (!empty($penulis)) {
                $query->where('mst_koleksi_buku.pengarang', 'LIKE', "%{$penulis}%");
            }

            if (!empty($kategori)) {
                $query->where('mst_koleksi_buku.id_ref_koleksi', $kategori);
            }

            $allowedSort = ['judul_koleksi', 'pengarang', 'tahun', 'id_ref_koleksi'];
            $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'judul_koleksi';

            return response()->json($query->orderBy($sortBy, $sortOrder)->paginate($perPage));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getKategoriBuku()
    {
        try {
            $kategori = DB::table('ref_koleksi')
                ->where('is_delete', 0)
                ->where('id_ref_koleksi', '!=', 4)
                // KEMBALIKAN KE DESKRIPSI
                ->orderBy('deskripsi')
                ->get(['id_ref_koleksi', 'deskripsi']); 

            return response()->json($kategori);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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

    /**
     * ==========================================
     * TAMBAHAN FITUR: PEMUSNAHAN BUKU (ISBN)
     * ==========================================
     */

    public function storePemusnahan(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string',
            'alasan' => 'required|string',
            'nip_karyawan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $buku = DB::table('mst_koleksi_buku')
                ->where('ISBN', $request->isbn)
                ->where('is_delete', 0)
                ->first();

            if (!$buku) {
                return response()->json(['message' => 'ISBN tidak ditemukan atau sudah dihapus.'], 404);
            }

            // Simpan ke tabel riwayat pemusnahan
            DB::table('tr_pemusnahan')->insert([
                'isbn' => $request->isbn,
                'alasan' => $request->alasan,
                'nip_karyawan' => $request->nip_karyawan,
                'tanggal_pemusnahan' => Carbon::now(),
                'status' => 'dimusnahkan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Kurangi stok fisik di mst_koleksi_buku
            if ($buku->jumlah_ekslempar > 0) {
                DB::table('mst_koleksi_buku')
                    ->where('ISBN', $request->isbn)
                    ->decrement('jumlah_ekslempar', 1);
            }

            DB::commit();
            return response()->json(['message' => 'Berhasil mencatat pemusnahan buku.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getHistoryPemusnahan(Request $request)
    {
        $search = $request->get('search');

        $query = DB::table('tr_pemusnahan')
            ->join('mst_koleksi_buku', 'tr_pemusnahan.isbn', '=', 'mst_koleksi_buku.ISBN')
            ->select(
                'tr_pemusnahan.*', 
                'mst_koleksi_buku.judul_koleksi as judul'
            )
            ->where('tr_pemusnahan.status', '!=', 'soft_deleted');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tr_pemusnahan.isbn', 'like', "%$search%")
                  ->orWhere('mst_koleksi_buku.judul_koleksi', 'like', "%$search%");
            });
        }

        return response()->json($query->orderBy('tr_pemusnahan.created_at', 'desc')->get());
    }

    public function getBukuRusak()
    {
        return response()->json(
            DB::table('mst_koleksi_buku')
                ->where('is_delete', 0)
                ->where('keterangan_buku', 'like', '%Rusak%')
                ->select('ISBN as isbn', 'judul_koleksi as judul', 'keterangan_buku as kondisi')
                ->get()
        );
    }

    public function getBukuOverdue()
    {
        return response()->json(
            DB::table('tr_peminjaman')
                ->join('cp_koleksi', 'tr_peminjaman.id_cp_koleksi', '=', 'cp_koleksi.id_cp_koleksi')
                ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
                ->whereNull('tr_peminjaman.tgl_kembali')
                ->where('tr_peminjaman.tgl_harus_kembali', '<', Carbon::now()->subDays(30))
                ->select(
                    'mst_koleksi_buku.ISBN as isbn', 
                    'mst_koleksi_buku.judul_koleksi as judul',
                    DB::raw('DATEDIFF(NOW(), tr_peminjaman.tgl_harus_kembali) as hari_terlambat')
                )
                ->get()
        );
    }

    public function updateStatusPemusnahan(Request $request, $id)
    {
        DB::table('tr_pemusnahan')->where('id', $id)->update([
            'status' => $request->get('status', 'soft_deleted'),
            'updated_at' => Carbon::now()
        ]);
        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function getAnggotaByIdentifier($identifier)
    {
        // 1. Cari di tabel siswa berdasarkan NISN
        $siswa = \DB::table('mst_siswa')
                    ->where('nisn_siswa', $identifier)
                    ->where('is_delete', 0)
                    ->first();

        if ($siswa) {
            return response()->json($siswa);
        }

        // 2. Jika tidak ada, cari di tabel karyawan berdasarkan NIP
        $karyawan = \DB::table('mst_karyawan')
                        ->where('nip_karyawan', $identifier)
                        ->where('is_delete', 0)
                        ->first();

        if ($karyawan) {
            return response()->json($karyawan);
        }

        // 3. Jika keduanya tidak ada
        return response()->json(['message' => 'Anggota tidak ditemukan'], 404);
    }
}