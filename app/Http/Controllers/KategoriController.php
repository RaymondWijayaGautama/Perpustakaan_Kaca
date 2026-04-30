<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RefKoleksi; // Hanya import model Kategori di sini

class KategoriController extends Controller
{
    /**
     * Tampilkan daftar kategori
     */
    public function index()
    {
        $kategori = RefKoleksi::where('IS_DELETE', 0)
            ->orWhereNull('IS_DELETE')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kategori
        ], 200);
    }

    /**
     * Simpan data kategori baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'NO_KATEGORI_BUKU' => 'nullable|string|max:10',
            'DESKRIPSI_KATEGORI' => 'required|string|max:255',
        ]);

        $kategori = RefKoleksi::create([
            'NO_KATEGORI_BUKU' => $request->NO_KATEGORI_BUKU,
            'DESKRIPSI_KATEGORI' => $request->DESKRIPSI_KATEGORI,
            'IS_DELETE' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => $kategori
        ], 201);
    }

    /**
     * Perbarui data kategori yang sudah ada
     */
    
}