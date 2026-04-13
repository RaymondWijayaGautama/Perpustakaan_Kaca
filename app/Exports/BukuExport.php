<?php

namespace App\Exports;

use App\Models\MstKoleksiBuku;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BukuExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    // 1. Logika Filter: Hanya mengambil data sesuai pencarian di web
    public function query()
    {
        $query = MstKoleksiBuku::query()->where('is_delete', 0);

        // Jika ada filter pencarian judul/isbn
        if ($this->request->has('search')) {
            $query->where('judul_koleksi', 'like', '%' . $this->request->search . '%')
                  ->orWhere('ISBN', 'like', '%' . $this->request->search . '%');
        }

        // Jika ada filter kategori
        if ($this->request->has('kategori') && $this->request->kategori != '') {
            $query->where('id_ref_koleksi', $this->request->kategori);
        }

        return $query;
    }

    // 2. Judul Kolom di Excel (Baris Pertama)
    public function headings(): array
    {
        return [
            'ISBN',
            'Judul Buku',
            'Pengarang',
            'Penerbit',
            'Tahun',
            'Kategori (ID)'
        ];
    }

    // 3. Pemetaan Data (Agar data yang keluar rapi)
    public function map($buku): array
    {
        return [
            $buku->ISBN,
            $buku->judul_koleksi,
            $buku->pengarang,
            $buku->penerbit,
            $buku->tahun,
            $buku->id_ref_koleksi,
        ];
    }
}