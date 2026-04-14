<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('tr_peminjaman')
            ->join('mst_siswa', 'tr_peminjaman.id_siswa_tetap', '=', 'mst_siswa.id_siswa')
            ->join('cp_koleksi', 'tr_peminjaman.cp_koleksi', '=', 'cp_koleksi.id_cp_koleksi')
            ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
            ->select(
                'tr_peminjaman.*', 
                'mst_siswa.nama as nama_peminjam', 
                'mst_koleksi_buku.judul as judul_buku'
            );

        if ($request->status && $request->status !== 'Semua') {
            $query->where('tr_peminjaman.status_peminjaman', $request->status);
        }

        return response()->json($query->orderBy('tgl_pinjam', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cp_koleksi' => 'required',
            'id_siswa_tetap' => 'required',
        ]);

        try {
            DB::beginTransaction();

            DB::table('tr_peminjaman')->insert([
                'tgl_pinjam' => Carbon::now(),
                'tgl_harus_kembali' => Carbon::now()->addDays(7), // Otomatis 7 hari
                'status_peminjaman' => 'Dipinjam',
                'cp_koleksi' => $request->id_cp_koleksi,
                'id_siswa_tetap' => $request->id_siswa_tetap,
                'nip_karyawan' => $request->nip_karyawan ?? 'P001',
                'kondisi_buku' => 'Baik',
                'denda_peminjaman' => 0
            ]);

            DB::table('cp_koleksi')
                ->where('id_cp_koleksi', $request->id_cp_koleksi)
                ->update(['status_buku' => 'Dipinjam']);

            DB::commit();
            return response()->json(['message' => 'Peminjaman berhasil dicatat!'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }
}
