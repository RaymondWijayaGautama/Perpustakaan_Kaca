<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanPklController extends Controller
{
    public function index(Request $request)
{
    try {
        $kategoriLaporan = DB::table('ref_koleksi')
                            ->where('NO_KATEGORI_BUKU', '4')
                            ->first();
        
        $query = DB::table('mst_koleksi_buku')
                    ->where('ID_REF_KOLEKSI', $kategoriLaporan?->ID_REF_KOLEKSI)
                    ->where('IS_DELETE', 0)
                    ->select('ISBN', 'JUDUL_KOLEKSI as judul_koleksi', 'PENGARANG as nama_siswa_tetap', 'TAHUN as tahun', 'KETERANGAN_BUKU as file_laporan');

        if ($request->filled('judul')) {
            $query->where('JUDUL_KOLEKSI', 'like', '%' . $request->judul . '%');
        }

        if ($request->filled('penulis')) {
            $query->where('PENGARANG', 'like', '%' . $request->penulis . '%');
        }

        $perPage = $request->input('per_page', 5);
        $data = $query->paginate($perPage);

        return response()->json($data);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}
}