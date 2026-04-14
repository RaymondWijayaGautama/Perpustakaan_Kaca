<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
   public function store(Request $request)
    {
        $request->validate([
            'id_cp_koleksi' => 'required', // Ini menerima ISBN 
            'id_siswa_tetap' => 'required', // Ini menerima NISN
        ]);

        $input_isbn = $request->id_cp_koleksi;

        // CARI BUKU BERDASARKAN ISBN
        $bukuTersedia = DB::table('cp_koleksi')
            ->where('ISBN', $input_isbn)
            ->where('status_buku', 'Tersedia')
            ->first();

        if (!$bukuTersedia) {
            return response()->json(['message' => "Gagal: Buku dengan ISBN $input_isbn tidak ditemukan atau stok sedang kosong/dipinjam semua!"], 404);
        }

        $id_koleksi_asli = $bukuTersedia->id_cp_koleksi; 

        // CARI SISWA BERDASARKAN NISN
        $siswa = DB::table('mst_siswa')->where('nisn_siswa', $request->id_siswa_tetap)->first();
        
        if (!$siswa) {
            return response()->json(['message' => 'Gagal: Siswa dengan NISN tersebut tidak ditemukan!'], 404);
        }
        
        $id_siswa_asli = $siswa->id_siswa_tetap; 

        try {
            DB::beginTransaction();

            DB::table('tr_peminjaman')->insert([
                'tgl_peminjaman'        => \Carbon\Carbon::now()->toDateString(),
                'tgl_harus_kembali'     => \Carbon\Carbon::now()->addDays(7)->toDateString(),
                'status_peminjaman'     => 'Dipinjam',
                'kondisi_buku'          => 'Baik',
                'keterangan_peminjaman' => '-', 
                'denda_peminjaman'      => 0,
                'id_cp_koleksi'         => $id_koleksi_asli, 
                'id_siswa_tetap'        => $id_siswa_asli,   
                'nip_karyawan'          => $request->nip_karyawan ?? 'P001'
            ]);

            DB::table('cp_koleksi')
                ->where('id_cp_koleksi', $id_koleksi_asli)
                ->update(['status_buku' => 'Dipinjam']);

            DB::commit();
            return response()->json(['message' => 'Peminjaman berhasil dicatat!'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }
}
