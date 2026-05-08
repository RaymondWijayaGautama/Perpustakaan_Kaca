<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengembalianExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID Peminjaman',
            'Tgl Kembali',
            'Nama Peminjam',
            'NISN/NIP',
            'Judul Buku',
            'Denda',
            'Kondisi Buku'
        ];
    }

    public function map($item): array
    {
        return [
            $item['id_peminjaman'] ?? '-',
            $item['tgl_kembali'] ?? '-',
            $item['nama_peminjam'] ?? '-',
            $item['nisn_nip'] ?? '-',
            $item['judul_koleksi'] ?? '-',
            $item['denda'] ?? 0,
            $item['kondisi_buku_kembali'] ?? 'Baik',
        ];
    }
}
