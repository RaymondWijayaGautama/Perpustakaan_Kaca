<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStats()
    {
        $totalBuku = DB::table('mst_koleksi_buku')
            ->where('is_delete', 0)
            ->where('id_ref_koleksi', '!=', 4) 
            ->sum('jumlah_ekslempar');

        $jumlahSiswa = DB::table('mst_siswa')->where('is_delete', 0)->count();
        $jumlahKaryawan = DB::table('mst_karyawan')->where('is_delete', 0)->count();

        $totalLaporan = DB::table('mst_koleksi_laporan')
            ->where('is_delete', 0)
            ->count();

        return response()->json([
            'total_buku' => (int)$totalBuku,
            'total_siswa' => $jumlahSiswa + $jumlahKaryawan,
            'total_laporan' => $totalLaporan
        ]);
    }

    public function getAnggota(Request $request)
    {
        try {
            $search = $request->query('search');
            $role = $request->query('role');
            $perPage = $request->get('per_page', 10);

            $siswa = DB::table('mst_siswa')
                ->where('is_delete', 0)
                ->select(
                    'id_siswa_tetap as id_internal', 
                    'nisn_siswa as identitas', 
                    'nama_siswa_tetap as nama', 
                    DB::raw("'Siswa' as role")
                );

            $karyawan = DB::table('mst_karyawan')
                ->where('is_delete', 0)
                ->select(
                    'nip_karyawan as id_internal', 
                    'nip_karyawan as identitas', 
                    'nama_karyawan as nama', 
                    DB::raw("'Karyawan' as role")
                );

            $unionQuery = $karyawan->union($siswa);
            $finalQuery = DB::query()->fromSub($unionQuery, 'combined_table');

            if (!empty($search)) {
                $finalQuery->where(function($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('identitas', 'LIKE', "%{$search}%"); 
                });
            }

            if (!empty($role) && $role !== 'Semua') {
                $finalQuery->where('role', '=', $role);
            }

            return response()->json($finalQuery->orderBy('nama', 'asc')->paginate($perPage));

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
        $validator = Validator::make($request->all(), [
            'editor_nip_karyawan' => ['required', 'string', 'max:20'],
            'judul_koleksi' => ['required', 'string', 'max:255'],
            'pengarang' => ['required', 'string', 'max:255'],
            'penerbit' => ['required', 'string', 'max:255'],
            'tahun' => ['required', 'digits:4'],
            'jumlah_ekslempar' => ['required', 'integer', 'min:0'],
            'no_rak_buku' => ['required', 'string', 'max:100'],
            'id_ref_koleksi' => ['required', 'integer', Rule::exists('ref_koleksi', 'id_ref_koleksi')->where('is_delete', 0)],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $isPustakawan = DB::table('mst_karyawan')
            ->where('nip_karyawan', $request->editor_nip_karyawan)
            ->where('jabatan_fungsional', 'Pustakawan')
            ->where('is_delete', 0)
            ->exists();

        if (!$isPustakawan) {
            return response()->json(['message' => 'Hanya pustakawan yang berwenang.'], 403);
        }

        DB::table('mst_koleksi_buku')->where('ISBN', $isbn)->update([
            'judul_koleksi' => $request->judul_koleksi,
            'pengarang' => $request->pengarang,
            'penerbit' => $request->penerbit,
            'tahun' => $request->tahun,
            'jumlah_ekslempar' => $request->jumlah_ekslempar,
            'no_rak_buku' => $request->no_rak_buku,
            'keterangan_buku' => $request->keterangan_buku ?: '',
            'id_ref_koleksi' => $request->id_ref_koleksi,
        ]);

        return response()->json(['message' => 'Koleksi berhasil diperbarui.']);
    }

    public function destroyBuku(Request $request, string $isbn)
    {
        DB::table('mst_koleksi_buku')->where('ISBN', $isbn)->update(['is_delete' => 1]);
        return response()->json(['message' => 'Buku berhasil dihapus.']);
    }

    public function storePemusnahan(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string',
            'alasan' => 'required|string',
            'nip_karyawan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            DB::table('tr_pemusnahan')->insert([
                'isbn' => $request->isbn,
                'alasan' => $request->alasan,
                'nip_karyawan' => $request->nip_karyawan,
                'tanggal_pemusnahan' => Carbon::now(),
                'status' => 'dimusnahkan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::table('mst_koleksi_buku')->where('ISBN', $request->isbn)->decrement('jumlah_ekslempar', 1);

            DB::commit();
            return response()->json(['message' => 'Pemusnahan berhasil dicatat.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getHistoryPemusnahan(Request $request)
    {
        return response()->json(
            DB::table('tr_pemusnahan')
                ->join('mst_koleksi_buku', 'tr_pemusnahan.isbn', '=', 'mst_koleksi_buku.ISBN')
                ->select('tr_pemusnahan.*', 'mst_koleksi_buku.judul_koleksi as judul')
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }
}