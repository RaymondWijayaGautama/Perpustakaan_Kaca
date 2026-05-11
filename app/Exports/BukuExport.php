<?php

namespace App\Exports;

use App\Models\MstKoleksiBuku;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting; // Tambahkan ini
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat; // Tambahkan ini

class BukuExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting, WithCustomValueBinder
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = MstKoleksiBuku::query()->where('is_delete', 0);

        if ($this->request->has('search') && $this->request->search != '') {
            $query->where(function ($q) {
                $q->where('judul_koleksi', 'like', '%' . $this->request->search . '%')
                  ->orWhere('ISBN', 'like', '%' . $this->request->search . '%');
            });
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

    /**
     * Mengatur format kolom secara global di Excel
     */
    public function columnFormats(): array
    {
        return [
            // Memaksa Kolom A (ISBN) menggunakan format TEXT murni
            'A' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function map($buku): array
    {
        // 1. Ambil angka mentahnya dulu (buang karakter aneh jika ada)
        $isbnMentah = preg_replace('/\D/', '', (string) $buku->ISBN);

        // 2. Jika panjangnya pas 13 digit, kita pakaikan format strip (-)
        if (strlen($isbnMentah) === 13) {
            $isbn = substr($isbnMentah, 0, 3) . '-' . 
                    substr($isbnMentah, 3, 3) . '-' . 
                    substr($isbnMentah, 6, 4) . '-' . 
                    substr($isbnMentah, 10, 2) . '-' . 
                    substr($isbnMentah, 12, 1);
        } else {
            // Jika digitnya kurang/lebih, tampilkan apa adanya
            $isbn = $buku->ISBN;
        }

        $isbnAntiError = ' ' . $isbn;

        return [
            $isbnAntiError, // Masukkan variabel yang sudah diberi spasi
            $buku->judul_koleksi,
            $buku->pengarang,
            $buku->penerbit,
            $buku->tahun,
            $buku->id_ref_koleksi,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'A') {
            // DataType::TYPE_STRING2 memastikan spasi/karakter tidak dibuang
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}