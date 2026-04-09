<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstKoleksiLaporan;
use Illuminate\Support\Facades\Validator;

class LaporanController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_laporan' => 'required|string|max:255',
            'penulis_laporan' => 'required|string|max:100',
            'tahun_laporan' => 'required|digits:4',
            'file_laporan' => 'required|file|mimes:pdf,doc,docx|max:10240', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'pesan' => $validator->errors()], 400);
        }
        $file = $request->file('file_laporan');
        $namaFile = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName()); 
        $path = $file->storeAs('public/laporan', $namaFile); 

        $laporan = MstKoleksiLaporan::create([
            'judul_laporan' => $request->judul_laporan,
            'penulis_laporan' => $request->penulis_laporan,
            'tahun_laporan' => $request->tahun_laporan,
            'file_path' => $namaFile,
            'is_delete' => 0
        ]);

        return response()->json([
            'status' => 'success',
            'pesan' => 'Laporan berhasil ditambahkan!',
            'data' => $laporan
        ], 201);
    }
}