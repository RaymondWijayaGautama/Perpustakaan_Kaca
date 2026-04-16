<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CpKoleksi;
use App\Models\MstKoleksiBuku;

class KoleksiController extends Controller
{
    public function index(Request $request)
    {
        $query = CpKoleksi::with('buku')
            ->where('is_delete', 0);

        // 🔍 SEARCH
        if ($request->search) {
            $query->whereHas('buku', function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('kategori', 'like', '%' . $request->search . '%');
            });
        }

        $koleksi = $query->paginate(10);

        return view('koleksi.index', compact('koleksi'));
    }

    public function create()
    {
        $buku = MstKoleksiBuku::all();
        return view('tambah_koleksi', compact('buku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ISBN' => 'required|exists:mst_koleksi_buku,ISBN',
            'jumlah' => 'required|integer|min:1',
            'lokasi_rak' => 'required',
            'kondisi_buku' => 'required'
        ]);

        for ($i = 0; $i < $request->jumlah; $i++) {
            dd($request->all());
            CpKoleksi::create([
                'id_koleksi' => uniqid(),
                'ISBN' => $request->ISBN,
                'status_buku' => 'Tersedia',
                'lokasi_rak' => $request->lokasi_rak,
                'tanggal_masuk' => now(),
                'kondisi_buku' => $request->kondisi_buku,
                'is_delete' => 0,
                'id_mst_laporan' => $request->id_mst_laporan // <- TAMBAH INI
            ]);
        }

        return redirect()->route('koleksi.index')->with('success', 'Berhasil tambah banyak koleksi');
    }

    public function edit($id)
    {
        $koleksi = CpKoleksi::findOrFail($id);
        return view('edit_koleksi', compact('koleksi'));
    }

    public function update(Request $request, $id)
    {
        $koleksi->update([
            'status_buku' => $request->status_buku,
            'lokasi_rak' => $request->lokasi_rak,
            'kondisi_buku' => $request->kondisi_buku
        ]);

        $koleksi = CpKoleksi::findOrFail($id);
        $koleksi->update($request->all());

        return redirect()->route('koleksi.index')->with('success', 'Koleksi berhasil diupdate');
    }

    public function destroy($id)
    {
        $koleksi = CpKoleksi::findOrFail($id);
        if ($koleksi->status_buku == 'Dipinjam') {
            return back()->with('error', 'Tidak bisa dihapus, buku sedang dipinjam');
        }

        $koleksi->update(['is_delete' => 1]);

        return back()->with('success', 'Koleksi berhasil dihapus');
    }
    public function buku()
    {
        return $this->belongsTo(MstKoleksiBuku::class, 'ISBN', 'ISBN');
    }
}