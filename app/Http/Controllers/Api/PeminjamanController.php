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
<<<<<<< Updated upstream
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
        
=======
        // 1. TANGKAP & PISAHKAN BARCODE
        $hasil_scan = trim($request->isbn); 
        $pecah = explode('-', $hasil_scan);
        if (count($pecah) < 2) {
            return response()->json(['message' => 'Gagal: Format Barcode salah! Harus mengandung ID fisik.'], 400);
        }
        
        $id_fisik = array_pop($pecah);      
        $isbn_murni = implode('-', $pecah); 
        
        // 2. CEK BUKU DI DATABASE
        $bukuFisik = DB::table('cp_koleksi')
            ->where('id_cp_koleksi', $id_fisik)
            ->where('ISBN', $isbn_murni) 
            ->first();
            
        if (!$bukuFisik) {
            return response()->json([
                'message' => "Gagal: Buku ID '$id_fisik' & ISBN '$isbn_murni' tidak ada di database!"
            ], 404);
        }

        if ($bukuFisik->status_buku !== 'Tersedia') {
            return response()->json([
                'message' => "Gagal: Buku fisik ini sedang tidak tersedia!"
            ], 400);
        }

        $siswa = DB::table('mst_siswa')->where('nisn_siswa', $request->id_siswa_tetap)->first();
            
>>>>>>> Stashed changes
        if (!$siswa) {
            return response()->json(['message' => 'Gagal: Siswa dengan NISN tersebut tidak terdaftar!'], 404);
        }
<<<<<<< Updated upstream
        
        $id_siswa_asli = $siswa->id_siswa_tetap; 
=======
>>>>>>> Stashed changes

        try {
            DB::beginTransaction();
            DB::table('tr_peminjaman')->insert([
                'id_cp_koleksi'         => $bukuFisik->id_cp_koleksi,
                'id_siswa_tetap'        => $siswa->id_siswa_tetap, 
                'nip_karyawan'          => $request->nip_karyawan,
                'tgl_peminjaman'        => now(),
                'tgl_harus_kembali'     => now()->addDays(7), 
                'status_peminjaman'     => 'Dipinjam',
                'kondisi_buku'          => 'Baik',  
                'keterangan_peminjaman' => '-',   
                'denda_peminjaman'      => 0        
            ]);

            DB::table('cp_koleksi')
                ->where('id_cp_koleksi', $bukuFisik->id_cp_koleksi)
                ->update(['status_buku' => 'Dipinjam']);

            DB::commit();
<<<<<<< Updated upstream
            return response()->json(['message' => 'Peminjaman berhasil dicatat!'], 201);
=======
            return response()->json(['message' => 'Peminjaman berhasil dicatat!']);
>>>>>>> Stashed changes

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal sistem: ' . $e->getMessage()], 500);
        }
    }
<<<<<<< Updated upstream
=======

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_peminjaman' => 'required',
            'kondisi_buku' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $peminjamanLama = DB::table('tr_peminjaman')->where('id_peminjaman', $id)->first();
            if (!$peminjamanLama) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            DB::table('tr_peminjaman')
                ->where('id_peminjaman', $id)
                ->update([
                    'status_peminjaman'     => $request->status_peminjaman,
                    'kondisi_buku'          => $request->kondisi_buku,
                    'keterangan_peminjaman' => $request->keterangan ?? '-',
                    'updated_at'            => now()
                ]);

            if ($request->status_peminjaman === 'Kembali') {
                DB::table('cp_koleksi')
                    ->where('id_cp_koleksi', $peminjamanLama->id_cp_koleksi)
                    ->update(['status_buku' => 'Tersedia']);
            }

            DB::commit();
            return response()->json(['message' => 'Data peminjaman berhasil diperbarui!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $peminjaman = DB::table('tr_peminjaman')->where('id_peminjaman', $id)->first();
            if (!$peminjaman) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            DB::table('tr_peminjaman')
                ->where('id_peminjaman', $id)
                ->update([
                    'status_peminjaman' => 'Dihapus', 
                    'updated_at'        => now()
                ]);

            if ($peminjaman->status_peminjaman === 'Dipinjam') {
                DB::table('cp_koleksi')
                    ->where('id_cp_koleksi', $peminjaman->id_cp_koleksi)
                    ->update(['status_buku' => 'Tersedia']);
            }
            DB::commit();
            return response()->json(['message' => 'Data transaksi berhasil diarsipkan (Soft Delete)!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
        }
    }
>>>>>>> Stashed changes
}