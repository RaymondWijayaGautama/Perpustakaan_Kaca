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

    public function query()
    {
        $query = MstKoleksiBuku::query()->where('is_delete', 0);

        if ($this->request->has('search')) {
            $query->where('judul_koleksi', 'like', '%' . $this->request->search . '%')
                  ->orWhere('ISBN', 'like', '%' . $this->request->search . '%');
        }

        if ($this->request->has('kategori') && $this->request->kategori != '') {
            $query->where('id_ref_koleksi', $this->request->kategori);
        }

        return $query;
    }

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