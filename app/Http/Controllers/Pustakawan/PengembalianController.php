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
        // Validasi Role Karyawan
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

    // =====================================================================
    // TAMBAHAN: FUNGSI BARU UNTUK MENGAMBIL DAN MENCARI DATA PENGEMBALIAN
    // =====================================================================
    public function history(Request $request)
    {
        $query = DB::table('tr_peminjaman as tp')
            // Join ke tabel koleksi & buku untuk dapat judul
            ->join('cp_koleksi as ck', 'tp.id_cp_koleksi', '=', 'ck.id_cp_koleksi')
            ->join('mst_koleksi_buku as buku', 'ck.ISBN', '=', 'buku.ISBN')
            // Join ke siswa & karyawan (LEFT JOIN karena peminjam bisa siswa atau karyawan)
            ->leftJoin('mst_siswa as ms', 'tp.ID_SISWA_TETAP', '=', 'ms.id_siswa_tetap')
            ->leftJoin('mst_karyawan as mk', 'tp.NIP_KARYAWAN', '=', 'mk.NIP_KARYAWAN')
            // Hanya ambil data yang SUDAH dikembalikan (tanggal kembali tidak kosong)
            ->whereNotNull('tp.tgl_kembali')
            ->select(
                'tp.id_tr_peminjaman as id_peminjaman',
                'tp.tgl_kembali as tgl_kembali',
                DB::raw('COALESCE(ms.nama_siswa_tetap, mk.NAMA_KARYAWAN) as nama_peminjam'),
                DB::raw('COALESCE(ms.nisn_siswa, mk.NIP_KARYAWAN) as nisn_nip'),
                'buku.judul_koleksi as judul_koleksi',
                // Catatan: Pastikan nama kolom 'denda' dan 'kondisi' di bawah ini 
                // sesuai dengan yang ada di struktur database lo ya!
                'tp.denda as denda', 
                'tp.kondisi as kondisi_buku_kembali' 
            );

        // Menangani Parameter Pencarian (Search) dari React
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('ms.nama_siswa_tetap', 'like', "%{$search}%")
                  ->orWhere('mk.NAMA_KARYAWAN', 'like', "%{$search}%")
                  ->orWhere('ms.nisn_siswa', 'like', "%{$search}%")
                  ->orWhere('mk.NIP_KARYAWAN', 'like', "%{$search}%")
                  ->orWhere('buku.judul_koleksi', 'like', "%{$search}%")
                  ->orWhere('tp.id_tr_peminjaman', 'like', "%{$search}%");
            });
        }

        // Urutkan data berdasarkan tanggal kembali paling baru
        $riwayat = $query->orderBy('tp.tgl_kembali', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $riwayat
        ]);
    }
}