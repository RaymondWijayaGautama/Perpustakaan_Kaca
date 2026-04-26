<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DB::table('tr_peminjaman as peminjaman')
                ->join('mst_siswa as siswa', 'peminjaman.ID_SISWA_TETAP', '=', 'siswa.ID_SISWA_TETAP')
                ->join('cp_koleksi as copy', 'peminjaman.ID_CP_KOLEKSI', '=', 'copy.ID_CP_KOLEKSI')
                ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
                ->select(
                    'peminjaman.ID_PEMINJAMAN as id_peminjaman',
                    'peminjaman.ID_CP_KOLEKSI as id_cp_koleksi',
                    'peminjaman.ID_SISWA_TETAP as id_siswa_tetap',
                    'peminjaman.NIP_KARYAWAN as nip_karyawan',
                    'peminjaman.TGL_PINJAM as tgl_peminjaman',
                    'peminjaman.TGL_HARUS_KEMBALI as tgl_harus_kembali',
                    'peminjaman.TGL_KEMBALI as tgl_kembali',
                    'peminjaman.STATUS_PEMINJAMAN as status_peminjaman',
                    'peminjaman.KONDISI_BUKU as kondisi_buku',
                    'peminjaman.KETERANGAN_PEMINJAMAN as keterangan_peminjaman',
                    'peminjaman.DENDA_PEMINJAMAN as denda_peminjaman',
                    'siswa.NAMA_SISWA_TETAP as nama_peminjam',
                    'siswa.NISN_SISWA as nisn_siswa',
                    'copy.ISBN',
                    'copy.STATUS_BUKU as status_buku',
                    'buku.JUDUL_KOLEKSI as judul_buku'
                );

            if ($request->status && $request->status !== 'Semua') {
                $query->where('peminjaman.STATUS_PEMINJAMAN', $request->status);
            }

            return response()->json($query->orderBy('peminjaman.TGL_PINJAM', 'desc')->get());
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {

        $hasil_scan = trim($request->isbn); 
        $pecah = explode('-', $hasil_scan);
        if (count($pecah) < 2) {
            return response()->json(['message' => 'Gagal Format Barcode salah ! Harus mengandung ID.'], 400);
        }
        
        $id_fisik = array_pop($pecah);      
        $isbn_murni = implode('-', $pecah); 
        $bukuFisik = DB::table('cp_koleksi')
            ->where('id_cp_koleksi', $id_fisik)
            ->where('ISBN', $isbn_murni) 
            ->first();
            
        if (!$bukuFisik) {
            return response()->json(['message' => "Gagal Buku ID '$id_fisik' & ISBN '$isbn_murni' tidak ada di database!"], 404);
        }

        if ($bukuFisik->status_buku !== 'Tersedia') {
            return response()->json(['message' => "Gagal ! Buku ini sedang dipinjam !"], 400);
        }

        $siswa = DB::table('mst_siswa')->where('nisn_siswa', $request->id_siswa_tetap)->first();
            
        if (!$siswa) {
            return response()->json(['message' => 'Gagal Siswa dengan NISN tersebut tidak terdaftar!'], 404);
        }

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
            return response()->json(['message' => 'Peminjaman berhasil dicatat!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal sistem: ' . $e->getMessage()], 500);
        }
    }

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
                ]);

            if ($peminjaman->status_peminjaman === 'Dipinjam') {
                DB::table('cp_koleksi')
                    ->where('id_cp_koleksi', $peminjaman->id_cp_koleksi)
                    ->update(['status_buku' => 'Tersedia']);
            }
            DB::commit();
            return response()->json(['message' => 'Data transaksi berhasil diarsipkan !']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus ' . $e->getMessage()], 500);
        }
    }

    public function cekAktif(Request $request)
    {
        $idMember = $request->id_member; // ID internal siswa/karyawan
        $inputBuku = $request->id_pinjam; // Input dari frontend (ISBN atau ID Peminjaman)

        // Melakukan JOIN untuk melacak ISBN melalui cp_koleksi
        $peminjaman = DB::table('tr_peminjaman')
            ->join('cp_koleksi', 'tr_peminjaman.id_cp_koleksi', '=', 'cp_koleksi.id_cp_koleksi')
            ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
            ->where('tr_peminjaman.id_siswa_tetap', $idMember)
            ->whereNull('tr_peminjaman.tgl_kembali') // Memastikan buku belum dikembalikan
            ->where(function ($query) use ($inputBuku) {
                // Cek apakah input cocok dengan ISBN atau ID Transaksi atau ID Fisik Buku
                $query->where('cp_koleksi.ISBN', $inputBuku)
                      ->orWhere('tr_peminjaman.id_peminjaman', $inputBuku)
                      ->orWhere('cp_koleksi.id_cp_koleksi', $inputBuku); 
            })
            ->select(
                'tr_peminjaman.id_peminjaman',
                'tr_peminjaman.id_cp_koleksi',
                'tr_peminjaman.tgl_harus_kembali',
                'mst_koleksi_buku.judul_koleksi',
                'cp_koleksi.ISBN'
            )
            ->first();

        if (!$peminjaman) {
            return response()->json(['message' => 'Buku tidak ditemukan atau tidak sedang dipinjam oleh pemustaka ini.'], 404);
        }

        return response()->json($peminjaman);
    }

    public function batchReturn(Request $request)
    {
        $items = $request->items;
        // Panggil class kalkulator
        $kalkulator = new \App\Http\Controllers\Pustakawan\KalkulasiKeterlambatanPengembalian();;

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                // Eksekusi kalkulasi keterlambatan
                $hasilKalkulasi = $kalkulator->hitung($item['tgl_harus_kembali'], $item['tgl_kembali_manual']);

                // Update status di tabel transaksi
                DB::table('tr_peminjaman')
                    ->where('id_peminjaman', $item['id_peminjaman'])
                    ->update([
                        'tgl_kembali' => $hasilKalkulasi['tgl_kembali'],
                        'status_peminjaman' => 'Selesai',
                        'kondisi_buku' => $item['kondisi'],
                        'keterangan_peminjaman' => $hasilKalkulasi['keterangan']
                    ]);

                // Update status fisik buku menjadi Tersedia
                DB::table('cp_koleksi')
                    ->where('id_cp_koleksi', $item['id_cp_koleksi'])
                    ->update(['status_buku' => 'Tersedia']);
            }

            DB::commit();
            return response()->json(['message' => 'Proses pengembalian berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses data: ' . $e->getMessage()], 500);
        }
    }
}
