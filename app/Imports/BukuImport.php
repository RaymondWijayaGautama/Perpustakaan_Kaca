<?php

namespace App\Imports;

use App\Models\MstKoleksiBuku;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon; // Untuk mengambil tanggal hari ini otomatis

class BukuImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return MstKoleksiBuku::updateOrCreate(
            ['ISBN' => $row['isbn']], 
            
            [
                'judul_koleksi'     => $row['judul'],
                'pengarang'         => $row['pengarang'],
                'tahun'             => $row['tahun'],
                'id_ref_koleksi'    => $row['id_kategori'],
                
                'penerbit'          => 'Belum diatur',
                'nb_koleksi'        => 1, // Angka default
                'tgl_masuk_koleksi' => Carbon::now()->format('Y-m-d'), 
                'jumlah_halaman'    => 0,
                'ukuran_buku'       => '-',
                'bibliografi'       => 'Tidak',
                'indeks_awal_akhir' => 0,
                'keterangan_buku'   => 'Buku baru dari import Excel',
                'no_rak_buku'       => 'Belum diatur',
                'is_delete'         => 0,
            ]
        );
    }
}