<?php

namespace App\Http\Controllers\Pustakawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    public function prosesKembali(Request $request)
    {
        // Validasi input
        $scannedCode = $request->id_peminjaman; 

        // Mencari peminjaman aktif
        // Sesuai migrasi: id_peminjaman
        $peminjaman = DB::table('tr_peminjaman')
            ->join('cp_koleksi', 'tr_peminjaman.id_cp_koleksi', '=', 'cp_koleksi.id_cp_koleksi')
            ->where(function($query) use ($scannedCode) {
                $query->where('tr_peminjaman.id_peminjaman', $scannedCode) // Perbaikan nama kolom
                      ->orWhere('cp_koleksi.ISBN', $scannedCode);
            })
            ->where(function($q) {
                $q->whereNull('tr_peminjaman.tgl_kembali')
                  ->orWhere('tr_peminjaman.tgl_kembali', '1970-01-01'); // Handle placeholder seeder
            })
            ->select('tr_peminjaman.*', 'cp_koleksi.id_cp_koleksi')
            ->first();

        if (!$peminjaman) {
            return response()->json(['message' => 'Data peminjaman tidak ditemukan atau buku sudah kembali.'], 404);
        }

        DB::beginTransaction();
        try {
            // Update transaksi peminjaman
            DB::table('tr_peminjaman')
                ->where('id_peminjaman', $peminjaman->id_peminjaman)
                ->update([
                    'tgl_kembali' => Carbon::now()->toDateString(),
                    'status_peminjaman' => 'Selesai'
                ]);

            // Update status fisik buku menjadi Tersedia
            DB::table('cp_koleksi')
                ->where('id_cp_koleksi', $peminjaman->id_cp_koleksi)
                ->update(['status_buku' => 'Tersedia']);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Buku berhasil dikembalikan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }
}