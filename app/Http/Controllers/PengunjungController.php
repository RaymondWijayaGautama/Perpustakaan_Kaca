<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengunjungController extends Controller
{
    // halaman utama
    public function index()
    {
        return view('pustakawan.pengunjung.index');
    }

public function search(Request $request)
{
    $keyword = $request->q;

    $data = DB::table('mst_siswa')
        ->where('is_delete', 0)
        ->where(function ($query) use ($keyword) {
            $query->where('nama_siswa_tetap', 'like', "%{$keyword}%")
                  ->orWhere('nisn_siswa', 'like', "%{$keyword}%")
                  ->orWhere('id_siswa_tetap', 'like', "%{$keyword}%");
        })
        ->limit(10)
        ->get();

    return response()->json($data);
}
}