<?php

namespace App\Exports;

use App\Models\MstKoleksiBuku;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BukuExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting, WithCustomValueBinder
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = MstKoleksiBuku::query()
            ->where('is_delete', 0);

        if ($this->request->filled('search')) {
            $search = $this->request->search;

            $query->where(function ($q) use ($search) {
                $q->where('judul_koleksi', 'like', '%' . $search . '%')
                  ->orWhere('ISBN', 'like', '%' . $search . '%');
            });
        }

        if ($this->request->filled('kategori')) {
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
            'Kategori (ID)',
        ];
    }

    public function map($buku): array
    {
        return [
            $this->formatText($buku->ISBN),
            $this->formatDash($buku->judul_koleksi),
            $this->formatDash($buku->pengarang),
            $this->formatDash($buku->penerbit),
            $this->formatDash($buku->tahun),
            $this->formatDash($buku->id_ref_koleksi),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'A') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    private function formatDash($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return (string) $value;
    }

    private function formatText($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return (string) $value;
    }
}