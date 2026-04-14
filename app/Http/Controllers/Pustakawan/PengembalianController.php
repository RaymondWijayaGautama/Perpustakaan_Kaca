<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    public function prosesKembali(Request $request)
    {
        // Validasi Role Karyawan [cite: 163]
        if ($request->role !== 'karyawan') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $scannedCode = $request->id_peminjaman; // Ini bisa berisi ISBN hasil scan

        // Mencari peminjaman aktif berdasarkan ID Transaksi ATAU ISBN Buku
        $peminjaman = DB::table('tr_peminjaman')
            ->join('cp_koleksi', 'tr_peminjaman.id_cp_koleksi', '=', 'cp_koleksi.id_cp_koleksi')
            ->where(function($query) use ($scannedCode) {
                $query->where('tr_peminjaman.id_tr_peminjaman', $scannedCode)
                      ->orWhere('cp_koleksi.ISBN', $scannedCode);
            })
            ->whereNull('tr_peminjaman.tgl_kembali')
            ->select('tr_peminjaman.*', 'cp_koleksi.id_cp_koleksi')
            ->first();

        if (!$peminjaman) {
            return response()->json(['message' => 'Buku dengan ISBN ini tidak sedang dipinjam.'], 404);
        }

        DB::beginTransaction();
        try {
            // Update transaksi peminjaman 
            DB::table('tr_peminjaman')
                ->where('id_tr_peminjaman', $peminjaman->id_tr_peminjaman)
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
            return response()->json(['message' => 'Gagal memperbarui data.'], 500);
        }
    }
}