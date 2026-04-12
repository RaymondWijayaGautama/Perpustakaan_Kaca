<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // WAJIB ADA untuk Query Builder
use Illuminate\Support\Facades\Validator; // WAJIB ADA untuk fungsi store
use App\Models\MstKoleksiLaporan;
use App\Models\CpKoleksi; // Pastikan model ini ada jika digunakan di destroy
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\Siswa;

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
            'judul_laporan' => 'required|string|max:255',
            'penulis_laporan' => 'required|string|max:100',
            'tahun_laporan' => 'required|digits:4',
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

    public function update(Request $request, $id)
    {
        $laporan = MstKoleksiLaporan::find($id);

        if (!$laporan) {
            return response()->json(['status' => 'error', 'pesan' => 'Data laporan tidak ditemukan!'], 404);
        }
        $validator = Validator::make($request->all(), [
            'judul_laporan' => 'required|string|max:255',
            'penulis_laporan' => 'required|string|max:100',
            'tahun_laporan' => 'required|digits:4',
            'file_laporan' => 'nullable|file|mimes:pdf,doc,docx|max:10240', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'pesan' => $validator->errors()], 400);
        }
        if ($request->hasFile('file_laporan')) {
            if (Storage::exists('public/laporan/' . $laporan->file_path)) {
                Storage::delete('public/laporan/' . $laporan->file_path);
            }
            $file = $request->file('file_laporan');
            $namaFile = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName()); 
            $file->storeAs('public/laporan', $namaFile);

            $laporan->file_path = $namaFile;
        }

        $laporan->judul_laporan = $request->judul_laporan;
        $laporan->penulis_laporan = $request->penulis_laporan;
        $laporan->tahun_laporan = $request->tahun_laporan;
        $laporan->save(); 

        return response()->json([
            'status' => 'success',
            'pesan' => 'Data laporan berhasil diubah!',
            'data' => $laporan
        ], 200);
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

    public function siswaTerajin()
    {
        
        $siswaTerajin = Siswa::withCount('peminjaman')
            ->orderBy('peminjaman_count', 'desc') 
            ->take(10) 
            ->get();

        return view('laporan.siswa_terajin', compact('siswaTerajin'));
    }

    public function exportPdfSiswaTerajin()
    {
    
    $siswaTerajin = Siswa::withCount('peminjaman')
        ->orderBy('peminjaman_count', 'desc')
        ->take(10)
        ->get();

    
    $data = [
        'siswaTerajin' => $siswaTerajin,
        'periode'      => 'Maret - April 2026',
        'tahun_ajaran' => '2025/2026', 
        'juara1'       => optional($siswaTerajin->get(0)),
        'juara2'       => optional($siswaTerajin->get(1)),
        'juara3'       => optional($siswaTerajin->get(2)),
    ];

    // Lempar sebagai HTML murni
    return view('laporan.pdf_siswa_terajin', $data);
    }
}