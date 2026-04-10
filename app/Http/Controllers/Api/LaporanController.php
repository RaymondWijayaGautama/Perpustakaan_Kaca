<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // WAJIB ADA untuk Query Builder
use Illuminate\Support\Facades\Validator; // WAJIB ADA untuk fungsi store
use App\Models\MstKoleksiLaporan;
use App\Models\CpKoleksi; // Pastikan model ini ada jika digunakan di destroy

class LaporanController extends Controller
{
    public function getLaporan(Request $request)
    {
        try {
            // Pastikan menggunakan Facade DB (sudah di-import di atas)
            $query = DB::table('mst_koleksi_buku')
                ->where('id_ref_koleksi', 4) // Angka 4 = Kategori PKL
                ->where('is_delete', 0)
                ->select(
                    'judul_koleksi', 
                    'pengarang as nama_siswa_tetap', 
                    'tahun'
                );

            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            if ($request->filled('penulis')) {
                $query->where('pengarang', 'like', '%' . $request->penulis . '%');
            }

            // Return data pagination (5 data per halaman)
            return response()->json($query->paginate(5));

        } catch (\Exception $e) {
            // Mengirim pesan error asli ke React untuk mempermudah debugging
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        // Validator sekarang sudah dikenali karena import di atas
        $validator = Validator::make($request->all(), [
            'id_mst_laporan' => 'required|integer|unique:mst_koleksi_laporan,id_mst_laporan',
            'file_laporan' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'pesan' => $validator->errors()], 400);
        }

        try {
            $file = $request->file('file_laporan');
            $namaFile = time() . '_' . $file->getClientOriginalName(); 
            $file->storeAs('public/laporan', $namaFile);

            $laporan = MstKoleksiLaporan::create([
                'id_mst_laporan' => $request->id_mst_laporan,
                'is_delete' => 0
            ]);

            return response()->json(['status' => 'success', 'data' => $laporan], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'pesan' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $laporan = MstKoleksiLaporan::find($id);

            if (!$laporan) {
                return response()->json(['status' => 'error', 'pesan' => 'Data tidak ditemukan!'], 404);
            }

            // Cek relasi ke tabel cp_koleksi
            $dipakai = DB::table('cp_koleksi')->where('id_mst_laporan', $id)->exists();
            if ($dipakai) {
                return response()->json(['status' => 'error', 'pesan' => 'Gagal! Laporan sedang terhubung dengan buku fisik.'], 400); 
            }

            $laporan->is_delete = 1;
            $laporan->save();

            return response()->json(['status' => 'success', 'pesan' => 'Berhasil dihapus!'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'pesan' => $e->getMessage()], 500);
        }
    }
}