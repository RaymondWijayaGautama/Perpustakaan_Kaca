<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    // =================================================================
    // 1. FUNGSI UNTUK MENAMPILKAN DATA DI TABEL RIWAYAT
    // =================================================================
    public function index(Request $request)
    {
        try {
            $query = DB::table('tr_peminjaman')
                ->join('mst_siswa', 'tr_peminjaman.id_siswa_tetap', '=', 'mst_siswa.id_siswa_tetap')
                ->join('cp_koleksi', 'tr_peminjaman.id_cp_koleksi', '=', 'cp_koleksi.id_cp_koleksi')
                ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
                ->select(
                    'tr_peminjaman.*', 
                    'mst_siswa.nama_siswa_tetap as nama_peminjam', 
                    'mst_koleksi_buku.judul_koleksi as judul_buku' 
                );

            // Filter Status (Aktif / Selesai)
            if ($request->status && $request->status !== 'Semua') {
                $query->where('tr_peminjaman.status_peminjaman', $request->status);
            }

            return response()->json($query->orderBy('tgl_peminjaman', 'desc')->get());
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // =================================================================
    // 2. FUNGSI UNTUK MENYIMPAN TRANSAKSI BARU (DARI SCAN BARCODE)
    // =================================================================
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