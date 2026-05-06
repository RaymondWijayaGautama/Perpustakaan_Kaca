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

   public function history(Request $request)
    {
        try {
            $query = DB::table('tr_peminjaman as tp')
                ->join('cp_koleksi as ck', 'tp.ID_CP_KOLEKSI', '=', 'ck.ID_CP_KOLEKSI')
                ->join('mst_koleksi_buku as buku', 'ck.ISBN', '=', 'buku.ISBN')
                ->leftJoin('mst_siswa as ms', 'tp.ID_SISWA_TETAP', '=', 'ms.ID_SISWA_TETAP')
                ->leftJoin('mst_karyawan as mk', 'tp.NIP_KARYAWAN', '=', 'mk.NIP_KARYAWAN')
                // KITA UBAH DISINI: Ambil yang TGL_KEMBALI ada isinya ATAU Statusnya 'Dikembalikan'
                ->where(function($q) {
                    $q->whereNotNull('tp.TGL_KEMBALI')
                      ->orWhere('tp.STATUS_PEMINJAMAN', 'Dikembalikan');
                })
                ->select(
                    'tp.ID_PEMINJAMAN',
                    'tp.TGL_KEMBALI',
                    'tp.STATUS_PEMINJAMAN',
                    'ms.NAMA_SISWA_TETAP',
                    'mk.NAMA_KARYAWAN',
                    'ms.NISN_SISWA',
                    'mk.NIP_KARYAWAN',
                    'buku.JUDUL_KOLEKSI',
                    'tp.DENDA_PEMINJAMAN',
                    'tp.KONDISI_BUKU'
                );

            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function($q) use ($search) {
                    $q->where('ms.NAMA_SISWA_TETAP', 'like', "%{$search}%")
                      ->orWhere('mk.NAMA_KARYAWAN', 'like', "%{$search}%")
                      ->orWhere('ms.NISN_SISWA', 'like', "%{$search}%")
                      ->orWhere('mk.NIP_KARYAWAN', 'like', "%{$search}%")
                      ->orWhere('buku.JUDUL_KOLEKSI', 'like', "%{$search}%")
                      ->orWhere('tp.ID_PEMINJAMAN', 'like', "%{$search}%");
                });
            }

            $riwayat = $query->orderBy('tp.ID_PEMINJAMAN', 'desc')->get();

            $formattedData = $riwayat->map(function ($item) {
                return [
                    'id_peminjaman' => $item->ID_PEMINJAMAN,
                    // Jika TGL_KEMBALI null tapi status Dikembalikan, kita beri tanda
                    'tgl_kembali' => $item->TGL_KEMBALI ?? 'Proses Kembali',
                    'nama_peminjam' => $item->NAMA_SISWA_TETAP ?? $item->NAMA_KARYAWAN ?? '-',
                    'nisn_nip' => $item->NISN_SISWA ?? $item->NIP_KARYAWAN ?? '-',
                    'judul_koleksi' => $item->JUDUL_KOLEKSI,
                    'denda' => $item->DENDA_PEMINJAMAN ?? 0,
                    'kondisi_buku_kembali' => $item->KONDISI_BUKU ?? 'Baik'
                ];
            });

            return response()->json(['status' => 'success', 'data' => $formattedData]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}